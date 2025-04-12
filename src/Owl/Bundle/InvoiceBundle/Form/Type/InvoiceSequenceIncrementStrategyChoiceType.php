<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InvoiceSequenceIncrementStrategyChoiceType extends AbstractResourceType
{
    public function __construct(private array $strategies)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => array_flip($this->strategies),
            ])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice_sequence_increment_strategy_choice';
    }
}
