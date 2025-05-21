<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid;

use Sylius\Bundle\GridBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;

trait GridFilterComponentTrait
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    protected FormFactoryInterface $formFactory;

    protected GridProviderInterface $gridProvider;

    protected FormTypeRegistryInterface $formTypeRegistry;

    protected RequestStack $requestStack;

    protected string $grid;

    protected function initialize(
        FormFactoryInterface $formFactory,
        GridProviderInterface $gridProvider,
        FormTypeRegistryInterface $formTypeRegistry,
        RequestStack $requestStack,
        string $grid
    ) {
        $this->formFactory = $formFactory;
        $this->gridProvider = $gridProvider;
        $this->formTypeRegistry = $formTypeRegistry;
        $this->requestStack = $requestStack;
        $this->grid = $grid;
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
    public function update(): void
    {
        $this->submitForm();
    }

    #[PreMount]
    public function getFiltersData(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->formValues = [];

        $criteria = $request->query->all('criteria') ?: [];

        if ($criteria) {
            $gridDefinition = $this->gridProvider->get($this->grid);

            foreach ($gridDefinition->getFilters() as $filter) {
                $value = $criteria[$filter->getName()] ?? null;
                $options = $filter->getOptions();

                if (!$this->isCustomFilter($options)) {
                    continue;
                }

                if (!empty($value)) {
                    $this->formValues[$filter->getName()] = $value;
                }
            }
        }

        $this->submitForm(false);
    }

    private function isCustomFilter(array $options): bool
    {
        return isset($options['custom']) && $options['custom'] === true;
    }
}
