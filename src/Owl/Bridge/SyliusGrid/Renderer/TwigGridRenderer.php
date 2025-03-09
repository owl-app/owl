<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Renderer;

use Sylius\Bundle\GridBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Bundle\ResourceBundle\Grid\Parser\OptionsParserInterface;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Definition\Grid;
use Twig\Environment;

final class TwigGridRenderer implements GridRendererInterface
{
    public function __construct(
        private GridRendererInterface $gridRenderer,
        private Environment $twig,
        private OptionsParserInterface $optionsParser,
        private ServiceRegistryInterface $fieldsRegistry,
        private FormFactoryInterface $formFactory,
        private FormTypeRegistryInterface $formTypeRegistry,
        private FormTypeRegistryInterface $formTypeElasticSearchRegistry,
        private FiltersCriteriaResolverInterface $criteriaResolver,
        private string $defaultTemplate,
        private array $actionTemplates = [],
        private array $filterTemplates = [],
        private array $filterElasticSearchTemplates = [],
    ) {

    }

    public function render(GridViewInterface $gridView, ?string $template = null): string
    {
        return $this->gridRenderer->render($gridView, $template);
    }

    /**
     * @param mixed $data
     */
    public function renderField(GridViewInterface $gridView, Field $field, $data): string
    {
        return $this->gridRenderer->renderField($gridView, $field, $data);
    }

    /**
     * @param mixed $data
     */
    public function renderAction(GridViewInterface $gridView, Action $action, $data = null): string
    {
        return $this->gridRenderer->renderAction($gridView, $action, $data);
    }

    public function renderFilter(GridViewInterface $gridView, Filter $filter): string
    {
        $grid = $gridView->getDefinition();
        $driverConfiguration = $grid->getDriverConfiguration();
        $isElasticsearch = $this->isElasticsearch($driverConfiguration, $grid, $gridView->getParameters());
        $template = $this->getFilterTemplate($isElasticsearch, $filter);

        if($isElasticsearch) {
            $formRegistry = $this->formTypeElasticSearchRegistry;
        } else {
            $formRegistry = $this->formTypeRegistry;
        }

        $form = $this->formFactory->createNamed('criteria', FormType::class, [], [
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'required' => false,
        ]);

        $form->add(
            $filter->getName(),
            $formRegistry->get($filter->getType(), 'default'),
            $filter->getFormOptions(),
        );

        $criteria = $gridView->getParameters()->get('criteria', []);
        $form->submit($criteria);

        return $this->twig->render($template, [
            'grid' => $gridView,
            'filter' => $filter,
            'form' => $form->get($filter->getName())->createView(),
        ]);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getFilterTemplate(bool $isElasticsearch, Filter $filter): string
    {
        if($isElasticsearch) {
            $filterTemplates = $this->filterElasticSearchTemplates;
        } else {
            $filterTemplates = $this->filterTemplates;
        }

        $template = $filter->getTemplate();
        if (null !== $template) {
            return $template;
        }

        $type = $filter->getType();
        if (!isset($filterTemplates[$type])) {
            throw new \InvalidArgumentException(sprintf('Missing template for filter type "%s".', $type));
        }

        return $filterTemplates[$type];
    }

    private function isElasticsearch(array $driverConfiguration, Grid $grid, Parameters $parameters): bool
    {
        return  isset($driverConfiguration['elasticsearch']['enabled']) && 
            $driverConfiguration['elasticsearch']['enabled'] && 
            $this->criteriaResolver->hasCriteria($grid, $parameters)
        ;
    }
}
