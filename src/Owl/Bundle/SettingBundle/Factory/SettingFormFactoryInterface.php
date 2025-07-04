<?php

declare(strict_types=1);

namespace Owl\Bundle\SettingBundle\Factory;

use Owl\Bridge\SyliusResource\Controller\RequestConfiguration;
use Symfony\Component\Form\FormInterface;

interface SettingFormFactoryInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param array<string, mixed> $data
     * @return FormInterface
     */
    public function create(RequestConfiguration $requestConfiguration, array $data): FormInterface;
}