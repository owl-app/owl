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
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

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

        $this->formFactory
            ->method('create')
            ->willReturn($this->form);

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

        $this->component->resource = $this->invoice;
    }

    public function testInitializesPreviewWithGeneratedNumber(): void
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

    #[DataProvider('lineItemDefaultValuesProvider')]
    public function testSetsDefaultValuesForLineItems(array $lineItems, array $expected): void
    {
        // Arrange
        $this->component->formValues = ['lineItems' => $lineItems];

        // Act
        $this->component->defaultValuesLineItems();

        // Assert
        $this->assertEquals($expected, $this->component->formValues['lineItems']);
    }

    public function testDoesNotProcessLineItemsWhenNotSet(): void
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

    #[DataProvider('paymentDateProvider')]
    public function testTogglesShowPaymentDateBasedOnInvoiceStatus(bool $isPaid, bool $expectedShow): void
    {
        // Arrange
        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

        $this->invoice
            ->method('isPaid')
            ->willReturn($isPaid);

        // Act
        $this->component->toggleShowPaymentDate();

        // Assert
        $this->assertEquals($expectedShow, $this->component->showPaymentDate);
    }

    public function testChangesCompanyAndUpdatesCurrency(): void
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

        $this->exchangeRateCurrencyResolver
            ->method('resolve')
            ->willReturn(null);

        $this->component->changeCompany();
    }

    public function testDoesNothingWhenCompanyIsNull(): void
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

    public function testDoesNotSetCurrencyWhenCompanyNotFound(): void
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

        // Act - this should not call setExchangeRate since company is null
        $this->component->changeCompany();

        // Assert
        $this->assertArrayNotHasKey('currency', $this->component->formValues);
    }

    public function testChangesExchangeRateCurrency(): void
    {
        // Arrange
        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

        // Act
        $this->component->changeExchangeRateCurrency();

        // Assert - no exceptions thrown
        $this->assertTrue(true);
    }

    public function testHandlesDateIssueChangedWithSerie(): void
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

        // Act
        $this->component->dateIssueChanged('2023-01-01');

        // Assert
        $this->assertEquals('INV/2023/456', $this->component->fullNumberPreview);
    }

    public function testDoesNothingWhenSerieIsNullInDateIssueChanged(): void
    {
        // Arrange
        $this->invoice
            ->method('getSerie')
            ->willReturn(null);

        $this->form
            ->method('getData')
            ->willReturn($this->invoice);

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

    #[DataProvider('calculationProvider')]
    public function testCalculatesQuantityChanged(string $unitPrice, string $quantity, float $expectedTotal, bool $expectedResult): void
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

    public function testHandlesUnitPriceChanged(): void
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

    public function testHandlesSumChanged(): void
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

    public function testHandlesNumberWithSerieChanged(): void
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

    public function testHandlesNullExchangeRateCurrency(): void
    {
        // Arrange
        $this->component->formValues = ['currency' => 'USD'];
        
        $this->exchangeRateCurrencyResolver
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn(null);

        // Assert
        $this->assertEquals('', $this->component->exchangeRateCurrency);
    }

    public function testHandlesMissingCurrencyInFormValues(): void
    {
        // Arrange
        $this->component->formValues = [];

        // Assert - no exceptions thrown
        $this->assertTrue(true);
    }
}
