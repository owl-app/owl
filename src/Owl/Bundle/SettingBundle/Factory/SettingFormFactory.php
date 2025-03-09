<?php

declare(strict_types=1);

namespace Owl\Bundle\SettingBundle\Factory;

use Owl\Bridge\SyliusResource\Controller\RequestConfiguration;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class SettingFormFactory implements SettingFormFactoryInterface
{
    public function __construct(private FormFactoryInterface $formFactory)
    {
    }

    public function create(RequestConfiguration $requestConfiguration, array $data): FormInterface
    {
        $formType = (string) $requestConfiguration->getFormType();
        $formOptions = $requestConfiguration->getFormOptions();

        if ($requestConfiguration->isHtmlRequest()) {
            return $this->formFactory->create($formType, $data, $formOptions);
        }

        return $this->formFactory->createNamed('', $formType, $data, array_merge($formOptions, ['csrf_protection' => false]));
    }
}
