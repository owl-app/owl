<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Action\Invoice;

use Owl\Bundle\AdminBundle\Action\Invoice\AvailabletSeriesAction;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twig\Environment;

#[CoversClass(AvailabletSeriesAction::class)]
final class AvailabletSeriesActionTest extends TestCase
{
    private AvailabletSeriesAction $action;
    private RepositoryInterface&MockObject $serieRepository;
    private InvoiceNumberGeneratorInterface&MockObject $invoiceNumberGenerator;
    private ServiceRegistryInterface&MockObject $registryInvoiceSequenceStrategy;
    private Environment&MockObject $twig;
    private Request&MockObject $request;
    private InvoiceSerieInterface&MockObject $invoiceSerie;
    private InvoiceSequenceStrategyInterface&MockObject $sequenceStrategy;
    private SequenceInterface&MockObject $invoiceSequence;

    protected function setUp(): void
    {
        $this->serieRepository = $this->createMock(RepositoryInterface::class);
        $this->invoiceNumberGenerator = $this->createMock(InvoiceNumberGeneratorInterface::class);
        $this->registryInvoiceSequenceStrategy = $this->createMock(ServiceRegistryInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->request = $this->createMock(Request::class);
        $this->invoiceSerie = $this->createMock(InvoiceSerieInterface::class);
        $this->sequenceStrategy = $this->createMock(InvoiceSequenceStrategyInterface::class);
        $this->invoiceSequence = $this->createMock(SequenceInterface::class);

        $this->action = new AvailabletSeriesAction(
            $this->serieRepository,
            $this->invoiceNumberGenerator,
            $this->registryInvoiceSequenceStrategy,
            $this->twig
        );
    }

    #[Test]
    public function it_renders_available_series_with_valid_data(): void
    {
        // Arrange
        $type = 'invoice';
        $dateString = '2024-01-15';
        $date = new \DateTime($dateString);
        $format = 'INV-{YYYY}-{MM}-{NNNN}';
        $nextCounter = 42;
        $nextValue = 'INV-2024-01-0042';
        $sequenceIncrement = 'monthly';
        $serieId = 1;

        $query = $this->createMock(ParameterBag::class);
        $query
            ->expects($this->once())
            ->method('get')
            ->with('date', '')
            ->willReturn($dateString);

        $this->request->query = $query;

        $this->serieRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['invoiceType' => $type])
            ->willReturn([$this->invoiceSerie]);

        $this->invoiceSerie
            ->expects($this->exactly(2))
            ->method('getSequenceIncrement')
            ->willReturn($sequenceIncrement);

        $this->invoiceSerie
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($serieId);

        $this->invoiceSerie
            ->expects($this->exactly(2))
            ->method('getFormat')
            ->willReturn($format);

        $this->registryInvoiceSequenceStrategy
            ->expects($this->once())
            ->method('get')
            ->with($sequenceIncrement)
            ->willReturn($this->sequenceStrategy);

        $this->sequenceStrategy
            ->expects($this->once())
            ->method('getNextCounter')
            ->with($this->invoiceSerie, $this->equalTo($date))
            ->willReturn($this->invoiceSequence);

        $this->invoiceSequence
            ->expects($this->exactly(2))
            ->method('getNextCounter')
            ->willReturn($nextCounter);

        $this->invoiceNumberGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($format, $nextCounter, $this->equalTo($date))
            ->willReturn($nextValue);

        $expectedSeries = [
            $serieId => [
                'id' => $serieId,
                'format' => $format,
                'nextCounter' => $nextCounter,
                'nextValue' => $nextValue,
                'sequenceIncrement' => $sequenceIncrement,
            ],
        ];

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/invoice/serie/available.html.twig', [
                'series' => $expectedSeries,
            ])
            ->willReturn('<html>available series</html>');

        // Act
        $response = ($this->action)($this->request, $type);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>available series</html>', $response->getContent());
    }

    #[Test]
    public function it_throws_exception_when_date_is_missing(): void
    {
        // Arrange
        $type = 'invoice';
        
        $query = $this->createMock(ParameterBag::class);
        $query
            ->expects($this->once())
            ->method('get')
            ->with('date', '')
            ->willReturn('');

        $this->request->query = $query;

        // Act & Assert
        try {
            ($this->action)($this->request, $type);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame('Date is required', $e->getMessage());
            $this->assertSame(Response::HTTP_BAD_REQUEST, $e->getStatusCode());
        }
    }

    #[Test]
    public function it_handles_empty_series_list(): void
    {
        // Arrange
        $type = 'invoice';
        $dateString = '2024-01-15';
        
        $query = $this->createMock(ParameterBag::class);
        $query
            ->expects($this->once())
            ->method('get')
            ->with('date', '')
            ->willReturn($dateString);

        $this->request->query = $query;

        $this->serieRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['invoiceType' => $type])
            ->willReturn([]);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/invoice/serie/available.html.twig', [
                'series' => [],
            ])
            ->willReturn('<html>no series</html>');

        // Act
        $response = ($this->action)($this->request, $type);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>no series</html>', $response->getContent());
    }
}
