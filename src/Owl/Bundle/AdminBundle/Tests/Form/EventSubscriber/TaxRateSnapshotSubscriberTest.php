<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Form\EventSubscriber;

use Owl\Bundle\AdminBundle\Form\EventSubscriber\TaxRateSnapshotSubscriber;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

#[CoversClass(TaxRateSnapshotSubscriber::class)]
final class TaxRateSnapshotSubscriberTest extends TestCase
{
    private TaxRateSnapshotSubscriber $subscriber;

    private FormInterface&MockObject $form;

    private FormEvent&MockObject $event;

    private LineItemInterface&MockObject $lineItem;

    private TaxRateInterface&MockObject $taxRate;

    private TaxRateSnapshotInterface&MockObject $taxRateSnapshot;

    private FormInterface&MockObject $taxRateForm;

    private FormConfigInterface&MockObject $taxRateConfig;

    private ResolvedFormTypeInterface&MockObject $taxRateType;

    protected function setUp(): void
    {
        $this->form = $this->createMock(FormInterface::class);
        $this->event = $this->createMock(FormEvent::class);
        $this->lineItem = $this->createMock(LineItemInterface::class);
        $this->taxRate = $this->createMock(TaxRateInterface::class);
        $this->taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $this->taxRateForm = $this->createMock(FormInterface::class);
        $this->taxRateConfig = $this->createMock(FormConfigInterface::class);
        $this->taxRateType = $this->createMock(ResolvedFormTypeInterface::class);

        $this->subscriber = new TaxRateSnapshotSubscriber();
    }

    #[Test]
    public function it_implements_event_subscriber_interface(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    #[Test]
    public function it_subscribes_to_correct_events(): void
    {
        $events = TaxRateSnapshotSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(FormEvents::POST_SET_DATA, $events);
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $events);
        $this->assertArrayHasKey(FormEvents::SUBMIT, $events);
        $this->assertSame('postSetData', $events[FormEvents::POST_SET_DATA]);
        $this->assertSame('preSubmit', $events[FormEvents::PRE_SUBMIT]);
        $this->assertSame('submit', $events[FormEvents::SUBMIT]);
    }

    #[Test]
    public function it_does_not_add_fields_when_line_item_is_null_on_post_set_data(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_adds_name_overwrite_field_when_tax_rate_name_is_different(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->any())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateNameDiffrent')
            ->willReturn(true);

        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateAmountDiffrent')
            ->willReturn(false);

        $this->form
            ->expects($this->once())
            ->method('get')
            ->with('taxRate')
            ->willReturn($this->taxRateForm);

        $this->taxRateForm
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->taxRateConfig);

        $this->taxRateConfig
            ->expects($this->once())
            ->method('getName')
            ->willReturn('taxRate');

        $this->taxRateConfig
            ->expects($this->once())
            ->method('getType')
            ->willReturn($this->taxRateType);

        $this->taxRateType
            ->expects($this->once())
            ->method('getInnerType')
            ->willReturn($this->createMock(\Symfony\Component\Form\FormTypeInterface::class));

        $this->taxRateConfig
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(['some' => 'options']);

        $callCount = 0;
        $this->form
            ->expects($this->exactly(2))
            ->method('add')
            ->willReturnCallback(function ($name, $type, $options = []) use (&$callCount) {
                ++$callCount;
                if ($callCount === 1) {
                    $this->assertEquals('snapshotNameOverwrite', $name);
                    $this->assertEquals(CheckboxType::class, $type);
                    $this->assertEquals('owl.ui.invoice.line_item.tax_rate_name_changed_overwrite', $options['label']);
                    $this->assertFalse($options['mapped']);
                    $this->assertFalse($options['required']);
                } elseif ($callCount === 2) {
                    $this->assertEquals('taxRate', $name);
                    // Typ będzie nazwą klasy mock'a, więc sprawdzamy czy zawiera FormTypeInterface
                    $this->assertStringContainsString('FormTypeInterface', $type);
                    $this->assertArrayHasKey('choice_label', $options);
                    $this->assertIsCallable($options['choice_label']);
                }

                return $this->form;
            });

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_adds_amount_overwrite_field_when_tax_rate_amount_is_different(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateNameDiffrent')
            ->willReturn(false);

        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateAmountDiffrent')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with('snapshotAmountOverwrite', CheckboxType::class, $this->callback(function (array $options) {
                return $options['label'] === 'owl.ui.invoice.line_item.tax_rate_amount_changed_overwrite' &&
                       $options['mapped'] === false &&
                       $options['required'] === false;
            }));

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_adds_both_overwrite_fields_when_both_name_and_amount_are_different(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateNameDiffrent')
            ->willReturn(true);

        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateAmountDiffrent')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('get')
            ->with('taxRate')
            ->willReturn($this->taxRateForm);

        $this->taxRateForm
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->taxRateConfig);

        $this->taxRateConfig
            ->expects($this->once())
            ->method('getName')
            ->willReturn('taxRate');

        $this->taxRateConfig
            ->expects($this->once())
            ->method('getType')
            ->willReturn($this->taxRateType);

        $this->taxRateType
            ->expects($this->once())
            ->method('getInnerType')
            ->willReturn($this->createMock(\Symfony\Component\Form\FormTypeInterface::class));

        $this->taxRateConfig
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(['some' => 'options']);

        $this->form
            ->expects($this->exactly(3))
            ->method('add');

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_returns_early_when_tax_rate_is_null_on_pre_submit(): void
    {
        // Arrange
        $data = ['taxRate' => null];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn(null);

        $this->lineItem
            ->expects($this->never())
            ->method('setTaxRateSnapshot');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_returns_early_when_tax_rate_snapshot_is_null_on_pre_submit(): void
    {
        // Arrange
        $data = ['taxRate' => 'TAX01'];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())  // Zmieniono z exactly(2) na once()
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn(null);

        $this->lineItem
            ->expects($this->never())
            ->method('setTaxRateSnapshot');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_returns_early_when_tax_rate_code_changed_on_pre_submit(): void
    {
        // Arrange
        $data = ['taxRate' => 'TAX02'];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRate
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX02');

        $this->taxRateSnapshot
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->lineItem
            ->expects($this->never())
            ->method('setTaxRateSnapshot');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_updates_name_when_snapshot_name_overwrite_is_enabled(): void
    {
        // Arrange
        $data = [
            'taxRate' => 'TAX01',
            'snapshotNameOverwrite' => 1,
        ];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->any())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRate
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Updated Tax Rate');

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('setName')
            ->with('Updated Tax Rate');

        $this->lineItem
            ->expects($this->once())
            ->method('setTaxRateSnapshot')
            ->with($this->taxRateSnapshot);

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_updates_amount_when_snapshot_amount_overwrite_is_enabled(): void
    {
        // Arrange
        $data = [
            'taxRate' => 'TAX01',
            'snapshotAmountOverwrite' => 1,
        ];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->any())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRate
            ->expects($this->once())
            ->method('getAmount')
            ->willReturn(25.00);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('setAmount')
            ->with(25.00);

        $this->lineItem
            ->expects($this->once())
            ->method('setTaxRateSnapshot')
            ->with($this->taxRateSnapshot);

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_updates_both_name_and_amount_when_both_overwrites_are_enabled(): void
    {
        // Arrange
        $data = [
            'taxRate' => 'TAX01',
            'snapshotNameOverwrite' => 1,
            'snapshotAmountOverwrite' => 1,
        ];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->any())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRate
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Updated Tax Rate');

        $this->taxRate
            ->expects($this->once())
            ->method('getAmount')
            ->willReturn(25.00);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('setName')
            ->with('Updated Tax Rate');

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('setAmount')
            ->with(25.00);

        $this->lineItem
            ->expects($this->once())
            ->method('setTaxRateSnapshot')
            ->with($this->taxRateSnapshot);

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_returns_early_when_tax_rate_is_null_on_submit(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn(null);

        $this->form
            ->expects($this->never())
            ->method('remove');

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_returns_early_when_tax_rate_snapshot_is_null_on_submit(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn(null);

        $this->form
            ->expects($this->never())
            ->method('remove');

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_removes_name_overwrite_field_when_tax_rate_code_changed_and_name_not_changed(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRate
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX02');

        $this->taxRateSnapshot
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isNameChanged')
            ->willReturn(false);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isAmountChanged')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('remove')
            ->with('snapshotNameOverwrite')
            ->willReturnSelf();

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_removes_amount_overwrite_field_when_tax_rate_code_changed_and_amount_not_changed(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRate
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX02');

        $this->taxRateSnapshot
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isNameChanged')
            ->willReturn(true);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isAmountChanged')
            ->willReturn(false);

        $this->form
            ->expects($this->once())
            ->method('remove')
            ->with('snapshotAmountOverwrite')
            ->willReturnSelf();

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_removes_both_overwrite_fields_when_tax_rate_code_changed_and_neither_name_nor_amount_changed(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRate
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX02');

        $this->taxRateSnapshot
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isNameChanged')
            ->willReturn(false);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isAmountChanged')
            ->willReturn(false);

        $removeCallCount = 0;
        $this->form
            ->expects($this->exactly(2))
            ->method('remove')
            ->willReturnCallback(function ($fieldName) use (&$removeCallCount) {
                ++$removeCallCount;
                if ($removeCallCount === 1) {
                    $this->assertEquals('snapshotNameOverwrite', $fieldName);
                } elseif ($removeCallCount === 2) {
                    $this->assertEquals('snapshotAmountOverwrite', $fieldName);
                }

                return $this->form;
            });

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_does_not_remove_fields_when_tax_rate_codes_are_same(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRate
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRateSnapshot
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->form
            ->expects($this->never())
            ->method('remove');

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_does_not_add_any_fields_when_tax_rate_snapshot_is_null_on_post_set_data(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn(null);

        // isTaxRateNameDiffrent() jest wywoływane nawet gdy snapshot jest null
        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateNameDiffrent')
            ->willReturn(false);

        // isTaxRateAmountDiffrent() też jest wywoływane
        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateAmountDiffrent')
            ->willReturn(false);

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_does_not_add_any_fields_when_both_name_and_amount_are_not_different(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateNameDiffrent')
            ->willReturn(false);

        $this->lineItem
            ->expects($this->once())
            ->method('isTaxRateAmountDiffrent')
            ->willReturn(false);

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_handles_string_values_for_overwrite_flags_in_pre_submit(): void
    {
        // Arrange
        $data = [
            'taxRate' => 'TAX01',
            'snapshotNameOverwrite' => '1',
            'snapshotAmountOverwrite' => '0',
        ];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->any())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRate
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Updated Tax Rate');

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('setName')
            ->with('Updated Tax Rate');

        $this->taxRateSnapshot
            ->expects($this->never())
            ->method('setAmount');

        $this->lineItem
            ->expects($this->once())
            ->method('setTaxRateSnapshot')
            ->with($this->taxRateSnapshot);

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_handles_empty_data_array_in_pre_submit(): void
    {
        // Arrange
        $data = [];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRateSnapshot
            ->expects($this->never())
            ->method('setName');

        $this->taxRateSnapshot
            ->expects($this->never())
            ->method('setAmount');

        $this->lineItem
            ->expects($this->never())
            ->method('setTaxRateSnapshot');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_handles_null_line_item_in_pre_submit(): void
    {
        // Arrange
        $data = ['taxRate' => 'TAX01'];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_handles_non_integer_overwrite_values_in_pre_submit(): void
    {
        // Arrange
        $data = [
            'taxRate' => 'TAX01',
            'snapshotNameOverwrite' => 'invalid',
            'snapshotAmountOverwrite' => 'also_invalid',
        ];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRateSnapshot
            ->expects($this->never())
            ->method('setName');

        $this->taxRateSnapshot
            ->expects($this->never())
            ->method('setAmount');

        $this->lineItem
            ->expects($this->never())
            ->method('setTaxRateSnapshot');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_handles_null_form_data_in_submit(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        $this->form
            ->expects($this->never())
            ->method('remove');

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_removes_both_fields_when_tax_rate_codes_differ_and_both_changes_are_false(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRate
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX02');

        $this->taxRateSnapshot
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isNameChanged')
            ->willReturn(false);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isAmountChanged')
            ->willReturn(false);

        $removeCallCount = 0;
        $this->form
            ->expects($this->exactly(2))
            ->method('remove')
            ->willReturnCallback(function ($fieldName) use (&$removeCallCount) {
                ++$removeCallCount;
                if ($removeCallCount === 1) {
                    $this->assertEquals('snapshotNameOverwrite', $fieldName);
                } elseif ($removeCallCount === 2) {
                    $this->assertEquals('snapshotAmountOverwrite', $fieldName);
                }

                return $this->form;
            });

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_does_not_remove_fields_when_tax_rate_codes_differ_but_both_changes_are_true(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->lineItem);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRate')
            ->willReturn($this->taxRate);

        $this->lineItem
            ->expects($this->once())
            ->method('getTaxRateSnapshot')
            ->willReturn($this->taxRateSnapshot);

        $this->taxRate
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX02');

        $this->taxRateSnapshot
            ->expects($this->any())
            ->method('getCode')
            ->willReturn('TAX01');

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isNameChanged')
            ->willReturn(true);

        $this->taxRateSnapshot
            ->expects($this->once())
            ->method('isAmountChanged')
            ->willReturn(true);

        $this->form
            ->expects($this->never())
            ->method('remove');

        // Act
        $this->subscriber->submit($this->event);
    }
}
