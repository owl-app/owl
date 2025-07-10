<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Controller;

use Owl\Component\Setting\Storage\SettingStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class DashboardController
{
    public function __construct(
        private SettingStorageInterface $settingStorage,
        private Environment $templatingEngine,
    ) {
    }

    public function indexAction(Request $request): Response
    {
        $settings = $this->settingStorage->getBySectionAndKeys('system', ['description_dashboard']);

        return new Response($this->templatingEngine->render('@OwlAdmin/dashboard/index.html.twig', [
            'settings' => $settings,
        ]));
    }
}
