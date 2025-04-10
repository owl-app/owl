<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Extension;

use Owl\Bundle\InvoiceBundle\Form\Type\InvoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class InvoiceTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

    }

    public static function getExtendedTypes(): iterable
    {
        return [InvoiceType::class];
    }
}
