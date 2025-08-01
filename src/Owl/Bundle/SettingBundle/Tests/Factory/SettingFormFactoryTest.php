<?php

declare(strict_types=1);

namespace Owl\Bundle\SettingBundle\Tests\Factory;

use Owl\Bridge\SyliusResource\Controller\RequestConfiguration;
use Owl\Bundle\SettingBundle\Factory\SettingFormFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class SettingFormFactoryTest extends TestCase
{
    private FormFactoryInterface $formFactory;
    private SettingFormFactory $settingFormFactory;
    private RequestConfiguration $requestConfiguration;

    protected function setUp(): void
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->settingFormFactory = new SettingFormFactory($this->formFactory);
        $this->requestConfiguration = $this->createMock(RequestConfiguration::class);
    }

    public function testCreatesFormForHtmlRequest(): void
    {
        $formType = 'TestFormType';
        $formOptions = ['option1' => 'value1'];
        $data = ['field1' => 'value1'];
        $expectedForm = $this->createMock(FormInterface::class);

        $this->requestConfiguration
            ->method('getFormType')
            ->willReturn($formType);

        $this->requestConfiguration
            ->method('getFormOptions')
            ->willReturn($formOptions);

        $this->requestConfiguration
            ->method('isHtmlRequest')
            ->willReturn(true);

        $this->formFactory
            ->method('create')
            ->with($formType, $data, $formOptions)
            ->willReturn($expectedForm);

        $result = $this->settingFormFactory->create($this->requestConfiguration, $data);

        $this->assertSame($expectedForm, $result);
    }

    public function testCreatesNamedFormForNonHtmlRequest(): void
    {
        $formType = 'TestFormType';
        $formOptions = ['option1' => 'value1'];
        $data = ['field1' => 'value1'];
        $expectedForm = $this->createMock(FormInterface::class);
        $expectedOptions = array_merge($formOptions, ['csrf_protection' => false]);

        $this->requestConfiguration
            ->method('getFormType')
            ->willReturn($formType);

        $this->requestConfiguration
            ->method('getFormOptions')
            ->willReturn($formOptions);

        $this->requestConfiguration
            ->method('isHtmlRequest')
            ->willReturn(false);

        $this->formFactory
            ->method('createNamed')
            ->with('', $formType, $data, $expectedOptions)
            ->willReturn($expectedForm);

        $result = $this->settingFormFactory->create($this->requestConfiguration, $data);

        $this->assertSame($expectedForm, $result);
    }

    public function testCreatesFormWithEmptyData(): void
    {
        $formType = 'TestFormType';
        $formOptions = [];
        $data = [];
        $expectedForm = $this->createMock(FormInterface::class);

        $this->requestConfiguration
            ->method('getFormType')
            ->willReturn($formType);

        $this->requestConfiguration
            ->method('getFormOptions')
            ->willReturn($formOptions);

        $this->requestConfiguration
            ->method('isHtmlRequest')
            ->willReturn(true);

        $this->formFactory
            ->method('create')
            ->with($formType, $data, $formOptions)
            ->willReturn($expectedForm);

        $result = $this->settingFormFactory->create($this->requestConfiguration, $data);

        $this->assertSame($expectedForm, $result);
    }

    public function testCreatesFormWithComplexData(): void
    {
        $formType = 'ComplexFormType';
        $formOptions = ['validation_groups' => ['Default']];
        $data = [
            'nested' => [
                'field1' => 'value1',
                'field2' => ['subfield' => 'subvalue']
            ]
        ];
        $expectedForm = $this->createMock(FormInterface::class);

        $this->requestConfiguration
            ->method('getFormType')
            ->willReturn($formType);

        $this->requestConfiguration
            ->method('getFormOptions')
            ->willReturn($formOptions);

        $this->requestConfiguration
            ->method('isHtmlRequest')
            ->willReturn(true);

        $this->formFactory
            ->method('create')
            ->with($formType, $data, $formOptions)
            ->willReturn($expectedForm);

        $result = $this->settingFormFactory->create($this->requestConfiguration, $data);

        $this->assertSame($expectedForm, $result);
    }
} 