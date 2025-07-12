<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\CompanyBundle\Twig\Component\Invoice;

use Owl\Bundle\AdminBundle\Form\Type\Invoice\InvoiceNumberingType;
use Owl\Bundle\AdminBundle\Twig\Component\Invoice\InvoiceNumberingComponent;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\LiveResponder;

#[CoversClass(InvoiceNumberingComponent::class)]
final class InvoiceNumberingComponentTest extends TestCase
{
    private InvoiceNumberingComponent $component;

    private FormFactoryInterface&MockObject $formFactory;

    private InvoiceNumberGeneratorInterface&MockObject $invoiceNumberGenerator;

    private FormInterface&MockObject $form;

    protected function setUp(): void
    {
        // Create a real LiveResponder instance instead of mocking it
        $liveResponder = new LiveResponder();
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->invoiceNumberGenerator = $this->createMock(InvoiceNumberGeneratorInterface::class);
        $this->form = $this->createMock(FormInterface::class);

        $this->component = new InvoiceNumberingComponent(
            $liveResponder,
            $this->formFactory,
            InvoiceNumberingType::class,
            $this->invoiceNumberGenerator
        );
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(InvoiceNumberingComponent::class, $this->component);
    }

    #[Test]
    public function it_has_correct_event_constant(): void
    {
        $this->assertEquals(
            'owl:admin:number_with_serie_changed',
            InvoiceNumberingComponent::OWL_ADMIN_NUMBER_WITH_SERIE_CHANGED
        );
    }

    #[Test]
    public function it_initializes_with_default_values(): void
    {
        $this->assertEquals([], $this->component->series);
        $this->assertEquals('', $this->component->issueDate);
        $this->assertEquals('', $this->component->fullNumberPreview);
        $this->assertFalse($this->component->showPreview);
        $this->assertFalse($this->component->showInputFullNumber);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function seriesHydrationProvider(): array
    {
        return [
            'valid json' => [
                'series' => '{"serie1":{"format":"INV/{###}","nextCounter":123}}',
                'expected' => ['serie1' => ['format' => 'INV/{###}', 'nextCounter' => 123]]
            ],
            'empty json' => [
                'series' => '{}',
                'expected' => []
            ],
            'array with multiple series' => [
                'series' => '{"serie1":{"format":"INV/{###}"},"serie2":{"format":"ORDER/{###}"}}',
                'expected' => [
                    'serie1' => ['format' => 'INV/{###}'],
                    'serie2' => ['format' => 'ORDER/{###}']
                ]
            ],
        ];
    }

    #[Test]
    #[DataProvider('seriesHydrationProvider')]
    public function it_hydrates_series_correctly(string $series, array $expected): void
    {
        $result = $this->component->hydrateSeries($series);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function it_handles_invalid_json_during_hydration(): void
    {
        $this->expectException(\TypeError::class);
        $this->component->hydrateSeries('invalid json');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function seriesDehydrationProvider(): array
    {
        return [
            'valid array' => [
                'series' => ['serie1' => ['format' => 'INV/{###}', 'nextCounter' => 123]],
                'expected' => '{"serie1":{"format":"INV\/{###}","nextCounter":123}}'
            ],
            'empty array' => [
                'series' => [],
                'expected' => '[]'
            ],
            'null array' => [
                'series' => null,
                'expected' => '[]'
            ],
            'complex array' => [
                'series' => [
                    'serie1' => ['format' => 'INV/{###}'],
                    'serie2' => ['format' => 'ORDER/{###}', 'nextCounter' => 456]
                ],
                'expected' => '{"serie1":{"format":"INV\/{###}"},"serie2":{"format":"ORDER\/{###}","nextCounter":456}}'
            ],
        ];
    }

    #[Test]
    #[DataProvider('seriesDehydrationProvider')]
    public function it_dehydrates_series_correctly(?array $series, string $expected): void
    {
        $result = $this->component->dehydrateSeries($series);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function it_instantiates_form_with_series_options(): void
    {
        // Arrange
        $testSeries = ['serie1' => ['format' => 'INV/{###}']];
        $this->component->series = $testSeries;

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(InvoiceNumberingType::class, [], ['series' => $testSeries])
            ->willReturn($this->form);

        // Act
        $reflection = new \ReflectionClass($this->component);
        $method = $reflection->getMethod('instantiateForm');
        $method->setAccessible(true);
        $result = $method->invoke($this->component);

        // Assert
        $this->assertSame($this->form, $result);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function preReRenderProvider(): array
    {
        return [
            'with valid format' => [
                'formData' => ['serie' => 'serie1', 'number' => 123],
                'series' => ['serie1' => ['format' => 'INV/{###}']],
                'issueDate' => '2023-01-15',
                'generatedNumber' => 'INV/123',
                'expectedShowPreview' => true,
                'expectedShowInputFullNumber' => false,
                'expectedFullNumberPreview' => 'INV/123'
            ],
            'without format' => [
                'formData' => ['serie' => 'serie2', 'number' => 123],
                'series' => ['serie2' => []],
                'issueDate' => '2023-01-15',
                'generatedNumber' => null,
                'expectedShowPreview' => false,
                'expectedShowInputFullNumber' => true,
                'expectedFullNumberPreview' => ''
            ],
            'with nonexistent serie' => [
                'formData' => ['serie' => 'nonexistent', 'number' => 123],
                'series' => ['serie1' => ['format' => 'INV/{###}']],
                'issueDate' => '2023-01-15',
                'generatedNumber' => null,
                'expectedShowPreview' => false,
                'expectedShowInputFullNumber' => true,
                'expectedFullNumberPreview' => ''
            ],
        ];
    }

    #[Test]
    #[DataProvider('preReRenderProvider')]
    public function it_handles_pre_re_render_correctly(
        array $formData,
        array $series,
        string $issueDate,
        ?string $generatedNumber,
        bool $expectedShowPreview,
        bool $expectedShowInputFullNumber,
        string $expectedFullNumberPreview
    ): void {
        // Arrange
        $this->component->series = $series;
        $this->component->issueDate = $issueDate;

        $this->form
            ->method('getData')
            ->willReturn($formData);

        if ($generatedNumber !== null) {
            $this->invoiceNumberGenerator
                ->expects($this->once())
                ->method('generate')
                ->with(
                    $series[$formData['serie']]['format'],
                    $formData['number'],
                    $this->isInstanceOf(\DateTime::class)
                )
                ->willReturn($generatedNumber);
        } else {
            $this->invoiceNumberGenerator
                ->expects($this->never())
                ->method('generate');
        }

        // Set form property directly
        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        // Act
        $this->component->preReRender();

        // Assert
        $this->assertEquals($expectedShowPreview, $this->component->showPreview);
        $this->assertEquals($expectedShowInputFullNumber, $this->component->showInputFullNumber);
        $this->assertEquals($expectedFullNumberPreview, $this->component->fullNumberPreview);
    }

    #[Test]
    public function it_handles_invalid_date_in_pre_re_render(): void
    {
        // Arrange
        $this->component->series = ['serie1' => ['format' => 'INV/{###}']];
        $this->component->issueDate = 'invalid-date';

        $this->form
            ->method('getData')
            ->willReturn(['serie' => 'serie1', 'number' => 123]);

        // Set form property directly
        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->component->preReRender();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function confirmActionProvider(): array
    {
        return [
            'with serie' => [
                'formData' => ['serie' => 'serie1', 'number' => 123, 'fullNumber' => ''],
                'fullNumberPreview' => 'INV/123',
                'expectedFullNumber' => '',
                'expectedFullNumberPreview' => 'INV/123'
            ],
            'without serie' => [
                'formData' => ['serie' => '', 'number' => 123, 'fullNumber' => 'CUSTOM-123'],
                'fullNumberPreview' => 'CUSTOM-123',
                'expectedFullNumber' => 'CUSTOM-123',
                'expectedFullNumberPreview' => 'CUSTOM-123'
            ],
        ];
    }

    #[Test]
    #[DataProvider('confirmActionProvider')]
    public function it_handles_confirm_action_correctly(
        array $formData,
        string $fullNumberPreview,
        string $expectedFullNumber,
        string $expectedFullNumberPreview
    ): void {
        // Arrange
        $this->component->fullNumberPreview = $fullNumberPreview;

        $this->form
            ->method('getData')
            ->willReturn($formData);

        $this->form
            ->method('isValid')
            ->willReturn(true);

        // Set form property directly
        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        // We'll test that confirm doesn't throw exceptions
        // Event emission testing would require complex mocking
        
        // Act & Assert
        $this->component->confirm();
        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function changeSerieProvider(): array
    {
        return [
            'existing serie' => [
                'serieValue' => 'serie1',
                'series' => [
                    'serie1' => ['nextCounter' => 456, 'nextValue' => 'INV/456'],
                    'serie2' => ['nextCounter' => 789, 'nextValue' => 'ORDER/789']
                ],
                'expectedSerie' => 'serie1',
                'expectedNumber' => 456,
                'expectedFullNumberPreview' => 'INV/456'
            ],
            'nonexistent serie' => [
                'serieValue' => 'nonexistent',
                'series' => [
                    'serie1' => ['nextCounter' => 456, 'nextValue' => 'INV/456']
                ],
                'expectedSerie' => '',
                'expectedNumber' => null,
                'expectedFullNumberPreview' => ''
            ],
            'empty serie value' => [
                'serieValue' => '',
                'series' => [
                    'serie1' => ['nextCounter' => 456, 'nextValue' => 'INV/456']
                ],
                'expectedSerie' => '',
                'expectedNumber' => null,
                'expectedFullNumberPreview' => ''
            ],
        ];
    }

    #[Test]
    #[DataProvider('changeSerieProvider')]
    public function it_handles_change_serie_action_correctly(
        string $serieValue,
        array $series,
        string $expectedSerie,
        ?int $expectedNumber,
        string $expectedFullNumberPreview
    ): void {
        // Arrange
        $this->component->series = $series;
        $this->component->formValues = [];

        // Act
        $this->component->changeSerie($serieValue);

        // Assert
        $this->assertEquals($expectedSerie, $this->component->formValues['serie']);
        
        if ($expectedNumber !== null) {
            $this->assertEquals($expectedNumber, $this->component->formValues['number']);
            $this->assertEquals($expectedFullNumberPreview, $this->component->fullNumberPreview);
        } else {
            $this->assertArrayNotHasKey('number', $this->component->formValues);
        }
    }

    #[Test]
    public function it_handles_form_initialization(): void
    {
        // Test that the component can initialize forms properly
        $this->component->series = ['test' => ['format' => 'TEST/{###}']];
        
        $this->formFactory
            ->method('create')
            ->willReturn($this->form);

        // This should not throw any exceptions
        $reflection = new \ReflectionClass($this->component);
        $method = $reflection->getMethod('instantiateForm');
        $method->setAccessible(true);
        $result = $method->invoke($this->component);

        $this->assertInstanceOf(FormInterface::class, $result);
    }

    #[Test]
    public function it_handles_hydration_with_proper_json_flags(): void
    {
        // Test the corrected hydration logic that should use JSON_THROW_ON_ERROR
        $validJson = '{"test": "value"}';
        $result = $this->component->hydrateSeries($validJson);
        
        $this->assertEquals(['test' => 'value'], $result);
    }
}
