<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type\Invoice;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class InvoiceSerieHiddenType extends AbstractType
{
    /** @param RepositoryInterface<InvoiceSerieInterface> $serieRepository */
    public function __construct(private RepositoryInterface $serieRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ResourceToIdentifierTransformer($this->serieRepository));
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice_serie_hidden';
    }
}
