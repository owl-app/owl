<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Form\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Owl\Bundle\AdminBundle\Form\EventSubscriber\LineItemsSubscriber;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\LineItemType;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Core\Model\CompanyInterface as CoreCompanyInterface;

#[CoversClass(LineItemsSubscriber::class)]
final class LineItemsSubscriberTest extends TestCase
{
    private LineItemsSubscriber $subscriber;
    private RepositoryInterface&MockObject $companyRepository;
    private FormInterface&MockObject $form;
    private FormEvent&MockObject $event;
    private InvoiceInterface&MockObject $invoice;
    private CoreCompanyInterface&MockObject $company;
    private CurrencyInterface&MockObject $currency;

    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(RepositoryInterface::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->event = $this->createMock(FormEvent::class);
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $this->company = $this->createMock(CoreCompanyInterface::class);
        $this->currency = $this->createMock(CurrencyInterface::class);

        $this->subscriber = new LineItemsSubscriber($this->companyRepository);
    }

    #[Test]
    public function it_implements_event_subscriber_interface(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    #[Test]
    public function it_subscribes_to_correct_events(): void
    {
        $events = LineItemsSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(FormEvents::POST_SET_DATA, $events);
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $events);
        $this->assertSame('postSetData', $events[FormEvents::POST_SET_DATA]);
        $this->assertSame('preSubmit', $events[FormEvents::PRE_SUBMIT]);
    }

    #[Test]
    public function it_creates_line_items_form_on_post_set_data(): void
    {
        // Arrange
        $currencyCode = 'USD';

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->invoice);

        $this->invoice
            ->expects($this->exactly(2))  // Zmieniono z once() na exactly(2)
            ->method('getCurrency')
            ->willReturn($this->currency);

        $this->invoice
            ->expects($this->once())
            ->method('getCompany')
            ->willReturn($this->company);

        $this->currency
            ->expects($this->once())
            ->method('getCode')
            ->willReturn($currencyCode);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with('lineItems', LiveCollectionType::class, $this->callback(function (array $options) use ($currencyCode) {
                return $options['entry_type'] === LineItemType::class &&
                       $options['entry_options']['currency'] === $currencyCode &&
                       $options['entry_options']['company'] === $this->company &&
                       $options['allow_add'] === true &&
                       $options['allow_delete'] === true &&
                       $options['by_reference'] === false;
            }));

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_does_not_create_form_when_invoice_is_not_invoice_interface(): void
    {
        // Arrange
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn('not an invoice');

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_does_not_create_form_when_invoice_has_no_currency(): void
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
            ->method('getCurrency')
            ->willReturn(null);

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->postSetData($this->event);
    }

    #[Test]
    public function it_creates_form_on_pre_submit_with_valid_data(): void
    {
        // Arrange
        $companyId = 123;
        $currencyCode = 'EUR';
        $data = [
            'company' => $companyId,
            'currency' => $currencyCode,
        ];

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
            ->method('has')
            ->with('lineItems')
            ->willReturn(false);

        $this->companyRepository
            ->expects($this->once())
            ->method('find')
            ->with($companyId)
            ->willReturn($this->company);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with('lineItems', LiveCollectionType::class, $this->callback(function (array $options) use ($currencyCode) {
                return $options['entry_type'] === LineItemType::class &&
                       $options['entry_options']['currency'] === $currencyCode &&
                       $options['entry_options']['company'] === $this->company;
            }));

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
            'empty company' => ['data' => ['company' => '', 'currency' => 'USD']],
            'empty currency' => ['data' => ['company' => 123, 'currency' => '']],
            'missing company' => ['data' => ['currency' => 'USD']],
            'missing currency' => ['data' => ['company' => 123]],
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

        $this->companyRepository
            ->expects($this->never())
            ->method('find');

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_does_not_create_form_when_line_items_already_exists(): void
    {
        // Arrange
        $data = [
            'company' => 123,
            'currency' => 'USD',
        ];

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
            ->method('has')
            ->with('lineItems')
            ->willReturn(true);

        $this->companyRepository
            ->expects($this->never())
            ->method('find');

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_does_not_create_form_when_company_not_found(): void
    {
        // Arrange
        $companyId = 123;
        $data = [
            'company' => $companyId,
            'currency' => 'USD',
        ];

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
            ->method('has')
            ->with('lineItems')
            ->willReturn(false);

        $this->companyRepository
            ->expects($this->once())
            ->method('find')
            ->with($companyId)
            ->willReturn(null);

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_does_not_create_form_when_found_object_is_not_company(): void
    {
        // Arrange
        $companyId = 123;
        $data = [
            'company' => $companyId,
            'currency' => 'USD',
        ];

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
            ->method('has')
            ->with('lineItems')
            ->willReturn(false);

        $this->companyRepository
            ->expects($this->once())
            ->method('find')
            ->with($companyId)
            ->willReturn(new \stdClass());

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_handles_repository_exception(): void
    {
        // Arrange
        $companyId = 123;
        $data = [
            'company' => $companyId,
            'currency' => 'USD',
        ];

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
            ->method('has')
            ->with('lineItems')
            ->willReturn(false);

        $this->companyRepository
            ->expects($this->once())
            ->method('find')
            ->with($companyId)
            ->willThrowException(new \RuntimeException('Repository error'));

        $this->form
            ->expects($this->never())
            ->method('add');

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Repository error');
        $this->subscriber->preSubmit($this->event);
    }

    #[Test]
    public function it_creates_line_items_form_with_correct_options(): void
    {
        // Arrange
        $currencyCode = 'PLN';

        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->invoice);

        $this->invoice
            ->expects($this->exactly(2))  // Zmieniono z once() na exactly(2)
            ->method('getCurrency')
            ->willReturn($this->currency);

        $this->invoice
            ->expects($this->once())
            ->method('getCompany')
            ->willReturn($this->company);

        $this->currency
            ->expects($this->once())
            ->method('getCode')
            ->willReturn($currencyCode);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with('lineItems', LiveCollectionType::class, $this->callback(function (array $options) use ($currencyCode) {
                $expectedOptions = [
                    'entry_type' => LineItemType::class,
                    'entry_options' => [
                        'company' => $this->company,
                        'currency' => $currencyCode,
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'button_add_options' => [
                        'label' => 'owl.ui.invoice.add_line_item',
                        'attr' => [
                            'class' => 'btn btn-secondary w-auto ps-5 pe-5 mt-3',
                        ],
                    ],
                    'button_delete_options' => [
                        'label' => false,
                        'row_attr' => [
                            'class' => 'mb-0',
                        ],
                    ],
                ];

                return $options['entry_type'] === $expectedOptions['entry_type'] &&
                       $options['entry_options'] === $expectedOptions['entry_options'] &&
                       $options['allow_add'] === $expectedOptions['allow_add'] &&
                       $options['allow_delete'] === $expectedOptions['allow_delete'] &&
                       $options['by_reference'] === $expectedOptions['by_reference'] &&
                       $options['button_add_options'] === $expectedOptions['button_add_options'] &&
                       $options['button_delete_options'] === $expectedOptions['button_delete_options'];
            }));

        // Act
        $this->subscriber->postSetData($this->event);
    }
}
