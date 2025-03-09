<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\View;

use Owl\Bridge\SyliusResource\Doctrine\Orm\CollectionProviderInterface;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParserInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridViewFactoryInterface;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Resource\Metadata\MetadataInterface;

final class ResourceGridViewFactory implements ResourceGridViewFactoryInterface
{
    public const DRIVER_ELASTICSEARCH = 'elasticsearch';

    public function __construct(
        private DataProviderInterface $dataProvider,
        private DataProviderInterface $elasticSearchDataProvider,
        private ParametersParserInterface $parametersParser,
        private FiltersCriteriaResolverInterface $criteriaResolver,
    ) {

    }

    public function create(
        Grid $grid,
        Parameters $parameters,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
    ): ResourceGridView {
        $driverConfiguration = $grid->getDriverConfiguration();
        $request = $requestConfiguration->getRequest();

        if (!isset($driverConfiguration['pre_load_event'])) {
            $driverConfiguration['pre_load_event'] = CollectionProviderInterface::TYPE;
        }

        $parsedDriverConfiguration = $this->parametersParser->parseRequestValues($driverConfiguration, $request);

        $grid->setDriverConfiguration($parsedDriverConfiguration);

        return new ResourceGridView($this->getData($grid, $parameters, $parsedDriverConfiguration), $grid, $parameters, $metadata, $requestConfiguration);
    }

    private function getData(Grid $grid, Parameters $parameters, array $parsedDriverConfiguration): mixed
    {
        $enabledElasticSearch = $parsedDriverConfiguration['elasticsearch']['enabled'] ?? false;

        if($enabledElasticSearch && $this->criteriaResolver->hasCriteria($grid, $parameters)) {
            return $this->elasticSearchDataProvider->getData($grid, $parameters);
        } 
        
        return $this->dataProvider->getData($grid, $parameters);
    }
}
