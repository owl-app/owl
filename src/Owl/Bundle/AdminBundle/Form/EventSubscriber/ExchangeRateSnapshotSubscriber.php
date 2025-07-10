<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\EventSubscriber;

use Owl\Bundle\AdminBundle\Form\Type\Invoice\ExchangeRateSnapshot;
use Owl\Component\Core\Invoice\Currency\ExchangeRateCurrencyResolverInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class ExchangeRateSnapshotSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ExchangeRateCurrencyResolverInterface $exchangeRateCurrencyResolver,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        /** @var InvoiceInterface|null $invoice */
        $invoice = $event->getData();

        if ($invoice === null || $invoice->getExchangeRateSnapshot() === null) {
            return;
        }

        $this->createExchangeRateSnapshotForm($form);
    }

    public function preSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!is_array($data) || !array_key_exists('exchangeRateSnapshot', $data)) {
            return;
        }

        $this->createExchangeRateSnapshotForm($form);
    }

    public function submit(FormEvent $event): void
    {
        $form = $event->getForm();
        /** @var InvoiceInterface|null $invoice */
        $invoice = $event->getData();

        if (
            !$form->has('exchangeRateSnapshot') && $invoice !== null && $this->exchangeRateCurrencyResolver->resolve($invoice)
        ) {
            $this->createExchangeRateSnapshotForm($form);
        }
    }

    private function createExchangeRateSnapshotForm(FormInterface $form): void
    {
        $form->add('exchangeRateSnapshot', ExchangeRateSnapshot::class);
    }
}
