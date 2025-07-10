<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Form\EventSubscriber;

use Owl\Bundle\AdminBundle\Form\EventSubscriber\ExchangeRateSnapshotSubscriber;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\ExchangeRateSnapshot;
use Owl\Component\Core\Invoice\Currency\ExchangeRateCurrencyResolverInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshotInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

#[CoversClass(ExchangeRateSnapshotSubscriber::class)]
final class ExchangeRateSnapshotSubscriberTest extends TestCase
{
    private ExchangeRateSnapshotSubscriber $subscriber;

    private ExchangeRateCurrencyResolverInterface&MockObject $exchangeRateCurrencyResolver;

    private FormInterface&MockObject $form;

    private FormEvent&MockObject $event;

    private InvoiceInterface&MockObject $invoice;

    protected function setUp(): void
    {
        $this->exchangeRateCurrencyResolver = $this->createMock(ExchangeRateCurrencyResolverInterface::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->event = $this->createMock(FormEvent::class);
        $this->invoice = $this->createMock(InvoiceInterface::class);

        $this->subscriber = new ExchangeRateSnapshotSubscriber(
            $this->exchangeRateCurrencyResolver,
        );
    }

    #[Test]
    public function it_implements_event_subscriber_interface(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    #[Test]
    public function it_subscribes_to_correct_events(): void
    {
        $events = ExchangeRateSnapshotSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $events);
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $events);
        $this->assertArrayHasKey(FormEvents::SUBMIT, $events);
        $this->assertSame('preSetData', $events[FormEvents::PRE_SET_DATA]);
        $this->assertSame('preSubmit', $events[FormEvents::PRE_SUBMIT]);
        $this->assertSame('submit', $events[FormEvents::SUBMIT]);
    }

    #[Test]
    public function it_creates_exchange_rate_snapshot_form_when_invoice_has_snapshot(): void
    {
        // Arrange
        $exchangeRateSnapshot = $this->createMock(ExchangeRateSnapshotInterface::class);

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->invoice);

        $this->invoice
            ->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with('exchangeRateSnapshot', ExchangeRateSnapshot::class);

        // Act
        $this->subscriber->preSetData($this->event);
    }

    #[Test]
    public function it_does_not_create_form_when_invoice_is_null(): void
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
        $this->subscriber->preSetData($this->event);
    }

    #[Test]
    public function it_does_not_create_form_when_invoice_has_no_snapshot(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->invoice);

        $this->invoice
            ->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn(null);

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->preSetData($this->event);
    }

    #[Test]
    public function it_creates_form_on_pre_submit_when_data_contains_exchange_rate_snapshot(): void
    {
        // Arrange
        $data = ['exchangeRateSnapshot' => ['rate' => 1.23]];

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with('exchangeRateSnapshot', ExchangeRateSnapshot::class);

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function invalidPreSubmitDataProvider(): array
    {
        return [
            'null data' => ['data' => null],
            'string data' => ['data' => 'invalid'],
            'array without exchangeRateSnapshot' => ['data' => ['other' => 'value']],
            'empty array' => ['data' => []],
        ];
    }

    #[Test]
    #[DataProvider('invalidPreSubmitDataProvider')]
    public function it_does_not_create_form_on_pre_submit_with_invalid_data(mixed $data): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_creates_form_on_submit_when_conditions_are_met(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->invoice);

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('exchangeRateSnapshot')
            ->willReturn(false);

        $currency = $this->createMock(CurrencyInterface::class);
        $this->exchangeRateCurrencyResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn($currency);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with('exchangeRateSnapshot', ExchangeRateSnapshot::class);

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_does_not_create_form_on_submit_when_form_already_has_field(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->invoice);

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('exchangeRateSnapshot')
            ->willReturn(true);

        $this->exchangeRateCurrencyResolver
            ->expects($this->never())
            ->method('resolve');

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_does_not_create_form_on_submit_when_invoice_is_null(): void
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
            ->expects($this->once())
            ->method('has')
            ->with('exchangeRateSnapshot')
            ->willReturn(false);

        $this->exchangeRateCurrencyResolver
            ->expects($this->never())
            ->method('resolve');

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_does_not_create_form_on_submit_when_resolver_returns_false(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->invoice);

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('exchangeRateSnapshot')
            ->willReturn(false);

        $this->exchangeRateCurrencyResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn(null);

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->submit($this->event);
    }

    #[Test]
    public function it_handles_exchange_rate_currency_resolver_exception(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->invoice);

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('exchangeRateSnapshot')
            ->willReturn(false);

        $this->exchangeRateCurrencyResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willThrowException(new \RuntimeException('Resolver error'));

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Resolver error');
        $this->subscriber->submit($this->event);
    }
}
