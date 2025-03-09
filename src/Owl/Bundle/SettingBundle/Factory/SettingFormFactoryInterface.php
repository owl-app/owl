<?php

declare(strict_types=1);

namespace Owl\Bundle\SettingBundle\Factory;

use Owl\Bridge\SyliusResource\Controller\RequestConfiguration;
use Symfony\Component\Form\FormInterface;

interface SettingFormFactoryInterface
{
    public function create(RequestConfiguration $requestConfiguration, array $data): FormInterface;
}
