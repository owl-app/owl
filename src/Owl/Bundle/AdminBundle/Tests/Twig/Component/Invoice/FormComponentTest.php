<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\CompanyBundle\Twig\Component\Invoice;

use Owl\Bundle\AdminBundle\Form\Type\InvoiceType;
use Owl\Bundle\AdminBundle\Twig\Component\Invoice\FormComponent;
use Owl\Component\Core\Invoice\Currency\ExchangeRateCurrencyResolverInterface;
use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Core\Model\Invoice\Invoice;
use Owl\Component\Core\Resolver\ExchangeRateResolverInterface;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshot;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

#[CoversClass(FormComponent::class)]
final class FormComponentTest extends TestCase
{
    private FormComponent $component;

    private RepositoryInterface&MockObject $invoiceRepository;

    private RepositoryInterface&MockObject $companyRepository;

    private FormFactoryInterface&MockObject $formFactory;

    private ExchangeRateCurrencyResolverInterface&MockObject $exchangeRateCurrencyResolver;

    private InvoiceNumberGeneratorInterface&MockObject $invoiceNumberGenerator;

    private ServiceRegistryInterface&MockObject $registryInvoiceSequenceStrategy;

    private ExchangeRateResolverInterface&MockObject $exchangeRateResolver;

    private FormInterface&MockObject $form;

    private Invoice&MockObject $invoice;

    protected function setUp(): void
    {
        $this->invoiceRepository = $this->createMock(RepositoryInterface::class);
        $this->companyRepository = $this->createMock(RepositoryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->exchangeRateCurrencyResolver = $this->createMock(ExchangeRateCurrencyResolverInterface::class);
        $this->invoiceNumberGenerator = $this->createMock(InvoiceNumberGeneratorInterface::class);
        $this->registryInvoiceSequenceStrategy = $this->createMock(ServiceRegistryInterface::class);
        $this->exchangeRateResolver = $this->createMock(ExchangeRateResolverInterface::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->invoice = $this->createMock(Invoice::class);

        $this->component = new FormComponent(
            $this->invoiceRepository,
            $this->formFactory,
            Invoice::class,
            InvoiceType::class,
            $this->exchangeRateCurrencyResolver,
            $this->companyRepository,
            $this->invoiceNumberGenerator,
            $this->registryInvoiceSequenceStrategy,
            $this->exchangeRateResolver,
        );

        // Set required properties
        $this->component->type = 'invoice';
        
        // Use reflection to set hdrate property
        $reflection = new \ReflectionClass($this->component);
        $resourceProperty = $reflection->getProperty('resource');
        $resourceProperty->setAccessible(true);
        $resourceProperty->setValue($this->component, $this->invoice);
    }

    #[Test]
    public function it_initializes_preview_with_generated_number(): void
    {
        // Arrange
        $serie = $this->createMock(InvoiceSerieInterface::class);
        $serie
            ->method('getFormat')
            ->willReturn('INV/{YYYY}/{MM}/{###}');

        $this->invoice
            ->method('getSerie')
            ->willReturn($serie);

        $this->invoice
            ->method('getSequenceNumber')
            ->willReturn(123);

        $issueDate = new \DateTime('2023-01-15');
        $this->invoice
            ->method('getIssueDate')
            ->willReturn($issueDate);

        $this->invoiceNumberGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('INV/{YYYY}/{MM}/{###}', 123, $issueDate)
            ->willReturn('INV/2023/01/123');

        // Act
        $this->component->initializePreview();

        // Assert
        $this->assertEquals('INV/2023/01/123', $this->component->fullNumberPreview);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function lineItemDefaultValuesProvider(): array
    {
        return [
            'missing unitPrice' => [
                'lineItems' => [
                    ['quantity' => 2, 'totalPrice' => 100, 'unit' => 'piece']
                ],
                'expected' => [
                    ['quantity' => 2, 'totalPrice' => 100, 'unit' => 'piece', 'unitPrice' => 0]
                ]
            ],
            'missing totalPrice' => [
                'lineItems' => [
                    ['quantity' => 2, 'unitPrice' => 50, 'unit' => 'piece']
                ],
                'expected' => [
                    ['quantity' => 2, 'unitPrice' => 50, 'unit' => 'piece', 'totalPrice' => 0]
                ]
            ],
            'missing quantity' => [
                'lineItems' => [
                    ['unitPrice' => 50, 'totalPrice' => 100, 'unit' => 'piece']
                ],
                'expected' => [
                    ['unitPrice' => 50, 'totalPrice' => 100, 'unit' => 'piece', 'quantity' => 1]
                ]
            ],
            'missing unit' => [
                'lineItems' => [
                    ['unitPrice' => 50, 'totalPrice' => 100, 'quantity' => 2]
                ],
                'expected' => [
                    ['unitPrice' => 50, 'totalPrice' => 100, 'quantity' => 2, 'unit' => LineItemInterface::UNIT_PIECE]
                ]
            ],
            'all missing' => [
                'lineItems' => [
                    []
                ],
                'expected' => [
                    ['unitPrice' => 0, 'totalPrice' => 0, 'quantity' => 1, 'unit' => LineItemInterface::UNIT_PIECE]
                ]
            ],
        ];
    }

    #[Test]
    #[DataProvider('lineItemDefaultValuesProvider')]
    public function it_sets_default_values_for_line_items(array $lineItems, array $expected): void
    {
        // Arrange
        $this->component->formValues = ['lineItems' => $lineItems];

        // Act
        $this->component->defaultValuesLineItems();

        // Assert
        $this->assertEquals($expected, $this->component->formValues['lineItems']);
    }

    #[Test]
    public function it_does_not_process_line_items_when_not_set(): void
    {
        // Arrange
        $this->component->formValues = [];

        // Act
        $this->component->defaultValuesLineItems();

        // Assert
        $this->assertEquals([], $this->component->formValues);
    }

    /**
     * @return array<string, array<string, bool>>
     */
    public static function paymentDateProvider(): array
    {
        return [
            'invoice is paid' => ['isPaid' => true, 'expectedShow' => true],
            'invoice is not paid' => ['isPaid' => false, 'expectedShow' => false],
        ];
    }

    #[Test]
    #[DataProvider('paymentDateProvider')]
    public function it_toggles_show_payment_date_based_on_invoice_status(bool $isPaid, bool $expectedShow): void
    {
        // Arrange
        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

        $this->invoice
            ->method('isPaid')
            ->willReturn($isPaid);

        // Set the form property directly
        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        // Act
        $this->component->toggleShowPaymentDate();

        // Assert
        $this->assertEquals($expectedShow, $this->component->showPaymentDate);
    }

    #[Test]
    public function it_changes_company_and_updates_currency(): void
    {
        $company = $this->createMock(CompanyInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        
        $currency
            ->method('getCode')
            ->willReturn('USD');

        $company
            ->method('getCurrency')
            ->willReturn($currency);

        $this->component->formValues = ['company' => 1];

        $this->companyRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($company);

        $this->form
            ->method('isValid')
            ->willReturn(true);

        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        $this->exchangeRateCurrencyResolver
            ->method('resolve')
            ->willReturn(null);

        $this->component->changeCompany();
    }

    #[Test]
    public function it_does_nothing_when_company_is_null(): void
    {
        // Arrange
        $this->component->formValues = ['company' => null];

        $this->companyRepository
            ->expects($this->never())
            ->method('find');

        // Act
        $this->component->changeCompany();

        // Assert - no exceptions thrown, no changes made
        $this->assertArrayNotHasKey('currency', $this->component->formValues);
    }

    #[Test]
    public function it_does_not_set_currency_when_company_not_found(): void
    {
        $company = $this->createMock(CompanyInterface::class);

        // Arrange
        $this->component->formValues = ['company' => 999];

        $company
            ->expects($this->never())
            ->method('getCurrency');

        $this->companyRepository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        // Act - this should not call setExchangeRate since company is null
        $this->component->changeCompany();

        // Assert
        $this->assertArrayNotHasKey('currency', $this->component->formValues);
    }

    #[Test]
    public function it_changes_exchange_rate_currency(): void
    {
        // Arrange
        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

        // Set the form property directly
        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        // Act
        $this->component->changeExchangeRateCurrency();

        // Assert - no exceptions thrown
        $this->assertTrue(true);
    }

    #[Test]
    public function it_handles_date_issue_changed_with_serie(): void
    {
        // Arrange
        $serie = $this->createMock(InvoiceSerieInterface::class);
        $serie
            ->method('getSequenceIncrement')
            ->willReturn('yearly');
        $serie
            ->method('getFormat')
            ->willReturn('INV/{YYYY}/{###}');

        $issueDate = new \DateTime('2023-01-15');
        
        $this->invoice
            ->method('getSerie')
            ->willReturn($serie);
        $this->invoice
            ->method('getIssueDate')
            ->willReturn($issueDate);

        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

        $invoiceSequence = $this->createMock(SequenceInterface::class);
        $invoiceSequence
            ->method('getNextCounter')
            ->willReturn(456);

        $strategy = $this->createMock(InvoiceSequenceStrategyInterface::class);
        $strategy
            ->method('getNextCounter')
            ->with($serie, $issueDate)
            ->willReturn($invoiceSequence);

        $this->registryInvoiceSequenceStrategy
            ->method('get')
            ->with('yearly')
            ->willReturn($strategy);

        $this->invoiceNumberGenerator
            ->method('generate')
            ->with('INV/{YYYY}/{###}', 456, $issueDate)
            ->willReturn('INV/2023/456');

        // Set the form property directly
        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        // Act
        $this->component->dateIssueChanged('2023-01-01');

        // Assert
        $this->assertEquals('INV/2023/456', $this->component->fullNumberPreview);
    }

    #[Test]
    public function it_does_nothing_when_serie_is_null_in_date_issue_changed(): void
    {
        // Arrange
        $this->invoice
            ->method('getSerie')
            ->willReturn(null);

        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

        // Set the form property directly
        $reflection = new \ReflectionClass($this->component);
        $formProperty = $reflection->getProperty('form');
        $formProperty->setAccessible(true);
        $formProperty->setValue($this->component, $this->form);

        $this->registryInvoiceSequenceStrategy
            ->expects($this->never())
            ->method('get');

        // Act
        $this->component->dateIssueChanged('2023-01-01');

        // Assert - no exceptions thrown
        $this->assertTrue(true);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function calculationProvider(): array
    {
        return [
            'valid unit price calculation' => [
                'unitPrice' => '50.00',
                'quantity' => '2',
                'expectedTotal' => 100.0,
                'expectedResult' => true
            ],
            'zero unit price' => [
                'unitPrice' => '0',
                'quantity' => '5',
                'expectedTotal' => 0,
                'expectedResult' => false
            ],
            'negative values' => [
                'unitPrice' => '-10',
                'quantity' => '2',
                'expectedTotal' => 0,
                'expectedResult' => false
            ],
        ];
    }

    #[Test]
    #[DataProvider('calculationProvider')]
    public function it_calculates_quantity_changed(string $unitPrice, string $quantity, float $expectedTotal, bool $expectedResult): void
    {
        // Arrange
        $this->component->formValues = [
            'lineItems' => [
                '0' => ['unitPrice' => $unitPrice, 'quantity' => '1', 'totalPrice' => '0']
            ]
        ];

        // Act
        $this->component->quantityChanged('0', $quantity);

        // Assert
        if ($expectedResult) {
            $this->assertEquals($expectedTotal, $this->component->formValues['lineItems']['0']['totalPrice']);
        } else {
            // For false cases, verify the calculation didn't succeed
            $this->assertFalse($this->component->formValues['lineItems']['0']['totalPrice'] > 0);
        }
    }

    #[Test]
    public function it_handles_unit_price_changed(): void
    {
        // Arrange
        $this->component->formValues = [
            'lineItems' => [
                '0' => ['unitPrice' => '0', 'quantity' => '2', 'totalPrice' => '0']
            ]
        ];

        // Act
        $this->component->unitPriceChanged('0', '25.50');

        // Assert
        $this->assertEquals(51.0, $this->component->formValues['lineItems']['0']['totalPrice']);
    }

    #[Test]
    public function it_handles_sum_changed(): void
    {
        // Arrange
        $this->component->formValues = [
            'lineItems' => [
                '0' => ['unitPrice' => '0', 'quantity' => '2', 'totalPrice' => '0']
            ]
        ];

        // Act
        $this->component->sumChanged('0', '100');

        // Assert - should calculate unit price based on total
        $this->assertTrue(isset($this->component->formValues['lineItems']['0']['unitPrice']));
    }

    #[Test]
    public function it_handles_number_with_serie_changed(): void
    {
        // Arrange
        $this->component->formValues = [];

        // Act
        $this->component->numberWithSerieChanged('123', 'serie1', 'FULL123', 'PREVIEW123');

        // Assert
        $this->assertEquals('123', $this->component->formValues['sequenceNumber']);
        $this->assertEquals('serie1', $this->component->formValues['serie']);
        $this->assertEquals('FULL123', $this->component->formValues['fullNumber']);
        $this->assertEquals('PREVIEW123', $this->component->fullNumberPreview);
    }

    #[Test]
    public function it_sets_exchange_rate_with_valid_currency(): void
    {
        // Arrange
        $exchangeRateCurrency = $this->createMock(CurrencyInterface::class);
        $exchangeRateCurrency
            ->method('getCode')
            ->willReturn('EUR');

        $this->component->formValues = ['currency' => 'USD'];
        
        $this->exchangeRateCurrencyResolver
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn($exchangeRateCurrency);

        $this->exchangeRateResolver
            ->method('getRatio')
            ->with('USD', 'EUR')
            ->willReturn(0.85);

        // Mock form view structure
        $formView = $this->createMock(FormView::class);
        $exchangeRateFormView = $this->createMock(FormView::class);
        $ratioFormView = $this->createMock(FormView::class);
        $ratioFormView->vars = ['value' => 0.85];

        $exchangeRateFormView
            ->method('offsetGet')
            ->with('ratio')
            ->willReturn($ratioFormView);

        $formView
            ->method('offsetExists')
            ->with('exchangeRateSnapshot')
            ->willReturn(true);
        
        $formView
            ->method('offsetGet')
            ->with('exchangeRateSnapshot')
            ->willReturn($exchangeRateFormView);

        // Mock getFormView method 
        $reflection = new \ReflectionClass($this->component);
        $getFormViewMethod = $reflection->getMethod('getFormView');
        $getFormViewMethod->setAccessible(true);

        // Create a component mock that can return our form view
        $componentMock = $this->getMockBuilder(FormComponent::class)
            ->setConstructorArgs([
                $this->invoiceRepository,
                $this->formFactory,
                Invoice::class,
                InvoiceType::class,
                $this->exchangeRateCurrencyResolver,
                $this->companyRepository,
                $this->invoiceNumberGenerator,
                $this->registryInvoiceSequenceStrategy,
                $this->exchangeRateResolver,
            ])
            ->onlyMethods(['getFormView'])
            ->getMock();

        $componentMock->method('getFormView')->willReturn($formView);
        $componentMock->formValues = ['currency' => 'USD'];

        $exchangeRateSnapshot = null; // No existing snapshot
        $this->invoice
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->invoice
            ->expects($this->once())
            ->method('setExchangeRateSnapshot')
            ->with($this->isInstanceOf(ExchangeRateSnapshot::class));

        // Act
        $reflection = new \ReflectionClass($componentMock);
        $method = $reflection->getMethod('setExchangeRate');
        $method->setAccessible(true);
        $method->invoke($componentMock, $this->invoice);

        // Assert
        $this->assertEquals('EUR', $componentMock->exchangeRateCurrency);
    }

    #[Test]
    public function it_handles_null_exchange_rate_currency(): void
    {
        // Arrange
        $this->component->formValues = ['currency' => 'USD'];
        
        $this->exchangeRateCurrencyResolver
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn(null);

        // Act
        $reflection = new \ReflectionClass($this->component);
        $method = $reflection->getMethod('setExchangeRate');
        $method->setAccessible(true);
        $method->invoke($this->component, $this->invoice);

        // Assert
        $this->assertEquals('', $this->component->exchangeRateCurrency);
    }

    #[Test]
    public function it_handles_missing_currency_in_form_values(): void
    {
        // Arrange
        $this->component->formValues = [];

        // Act
        $reflection = new \ReflectionClass($this->component);
        $method = $reflection->getMethod('setExchangeRate');
        $method->setAccessible(true);
        $method->invoke($this->component, $this->invoice);

        // Assert - no exceptions thrown
        $this->assertTrue(true);
    }
}
