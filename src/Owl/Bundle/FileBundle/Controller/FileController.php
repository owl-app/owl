<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\Controller;

use Owl\Bridge\SyliusResource\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

class FileController extends BaseController
{
    public function uploadAction(Request $request): void
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, 'upload');

        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $form = $this->resourceFormFactory->create($configuration, $newResource);
        $form->handleRequest($request);

        $newResource = $form->getData();

        $this->eventDispatcher->dispatch('upload', $configuration, $newResource);
    }
}
