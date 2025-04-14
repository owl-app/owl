<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Action\Invoice;

use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twig\Environment;

final class AvailabletSeriesAction
{
    public function __construct(
        private RepositoryInterface $serieRepository,
        private InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
        private ServiceRegistryInterface $registryInvoiceSequenceStrategy,
        private Environment $twig,
    ) {
    }

    public function __invoke(Request $request, string $type): Response
    {
        /** @var InvoiceSerieInterface[] $series */
        $series = $this->serieRepository->findBy(['invoiceType' => $type]);
        $date = new \DateTime((string) $request->query->get('date', ''));
        $availablesSeries = [];

        if (!$date) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Date is required');
        }

        foreach ($series as $serie) {
            $strategy = $this->registryInvoiceSequenceStrategy->get($serie->getSequenceIncrement());
            $invoiceSequence = $strategy->getNextCounter($serie, $date);

            $availablesSeries[] = [
                'id' => $serie->getId(),
                'format' => $serie->getFormat(),
                'nextCounter' => $invoiceSequence->getNextCounter(),
                'nextValue' => $this->invoiceNumberGenerator->generate($serie, $invoiceSequence->getNextCounter(), $date),
                'sequenceIncrement' => $serie->getSequenceIncrement(),
            ];
        }

        return new Response(
            $this->twig->render('@OwlAdmin/invoice/serie/available.html.twig', [
                'series' => $availablesSeries,
            ]),
        );
    }
}
