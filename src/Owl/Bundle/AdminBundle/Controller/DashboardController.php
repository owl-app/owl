<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Controller;

use Elastic\Elasticsearch\Client;
use Owl\Component\Setting\Storage\SettingStorageInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class DashboardController
{
    /**
     * @param Environment $templatingEngine
     */
    public function __construct(
        private SettingStorageInterface $settingStorage,
        private object $templatingEngine,
        private RouterInterface $router,
    ) {
    }

    public function indexAction(Request $request): Response
    {
        $settings = $this->settingStorage->getBySectionAndKeys('system', ['description_dashboard']);

        return new Response($this->templatingEngine->render('@OwlAdmin/Dashboard/index.html.twig', [
            'settings' => $settings,
        ]));
    }
}
