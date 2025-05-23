<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid;

use Owl\Component\Core\Manager\UserPreferenceManagerInterface;
use Sylius\Bundle\GridBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;

trait GridFilterComponentTrait
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use FilterDataComponentTrait;

    protected FormFactoryInterface $formFactory;

    protected GridProviderInterface $gridProvider;

    protected FormTypeRegistryInterface $formTypeRegistry;

    protected RequestStack $requestStack;

    protected UserPreferenceManagerInterface $userPreferenceManager;

    protected string $grid;

    protected function initialize(
        FormFactoryInterface $formFactory,
        GridProviderInterface $gridProvider,
        FormTypeRegistryInterface $formTypeRegistry,
        RequestStack $requestStack,
        UserPreferenceManagerInterface $userPreferenceManager,
        string $grid
    ) {
        $this->formFactory = $formFactory;
        $this->gridProvider = $gridProvider;
        $this->formTypeRegistry = $formTypeRegistry;
        $this->requestStack = $requestStack;
        $this->userPreferenceManager = $userPreferenceManager;
        $this->grid = $grid;
        $this->availableFilters = $this->getAvailableFilters();
    }

    protected function instantiateForm(): FormInterface
    {
        $form = $this->formFactory->createNamed('criteria', FormType::class, [], [
            'csrf_protection' => false,
        ]);
        $gridDefinition = $this->gridProvider->get($this->grid);

        foreach ($gridDefinition->getFilters() as $filter) {
            $options = $filter->getOptions();

            if (!$this->isCustomFilter($options)) {
                continue;
            }

            if (!$this->formTypeRegistry->has($filter->getType(), 'default')) {
                throw new \InvalidArgumentException(sprintf('Filter type "%s" is not registered.', $filter->getType()));
            }
            $form->add($filter->getName(), $this->formTypeRegistry->get($filter->getType(), 'default'), $filter->getFormOptions());
        }

        return $form;
    }

    #[LiveAction]
    public function updateAll(): void
    {
        $this->submitForm();

        $this->activeCriteria = array_replace($this->activeCriteria, $this->formValues);
    }

    #[LiveAction]
    public function updateFilter(#[LiveArg] $field): void
    {
        $this->formValues = array_merge(
            array_filter($this->activeCriteria, fn($value) => in_array($value, $this->availableFilters)),
            [$field => $this->formValues[$field] ?? []]
        );

        $this->submitForm();

        $this->activeCriteria = array_replace($this->activeCriteria, $this->formValues);

        $this->saveUserPreference($field);
    }

    #[PreMount]
    public function getFiltersData(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->formValues = [];

        $criteria = $request->query->all('criteria') ?: [];

        $gridDefinition = $this->gridProvider->get($this->grid);

        foreach ($gridDefinition->getFilters() as $filter) {
            $value = $criteria[$filter->getName()] ?? null;
            $options = $filter->getOptions();

            if (!$this->isCustomFilter($options)) {
                continue;
            }

            if (!empty($value)) {
                $this->formValues[$filter->getName()] = $value;
            } else {
                $filterKey = $this->getFilterKey($gridDefinition, $filter->getName());
                $this->formValues[$filter->getName()] = $this->userPreferenceManager->get($filterKey);
            }
        }

        $this->activeCriteria = array_replace_recursive($criteria, $this->formValues);
        $this->submitForm(false);
    }

    private function isCustomFilter(array $options): bool
    {
        return isset($options['custom']) && $options['custom'] === true;
    }

    private function getAvailableFilters(): array
    {
        $availableFilters = [];
        $gridDefinition = $this->gridProvider->get($this->grid);

        foreach ($gridDefinition->getFilters() as $filter) {
            $options = $filter->getOptions();

            if (!$this->isCustomFilter($options)) {
                continue;
            }

            if (isset($availableFilters[$filter->getType()])) {
                array_push($this->availableFilters[$filter->getType()], $filter->getName());
            } else {
                $availableFilters[$filter->getType()] = [$filter->getName()];
            }
        }

        return $availableFilters;
    }

    private function saveUserPreference(string $field): void
    {
        $gridDefinition = $this->gridProvider->get($this->grid);
        $filter = $gridDefinition->getFilter($field);

        if (!$filter->getOptions()['saved'] ?? false) {
            return;
        }

        if (isset($this->formValues[$field])) {
            $this->userPreferenceManager->update($this->getFilterKey($gridDefinition, $field), $this->formValues[$field]);
        }
    }

    private function getFilterKey(Grid $gridDefinition, string $name): ?string
    {
        return 'filters.' . $gridDefinition->getCode() . '.' .$name;
    }
}
