<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type\Invoice;

use Owl\Bundle\AdminBundle\Form\Type\ContractorAutocompleteType;
use Owl\Component\Core\Model\Invoice\Buyer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class InvoiceNumberingType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serie', ChoiceType::class, [
                'label' => 'Wybierz kolor',
                'choices' =>  $this->getSeriesChoices($options['series']),
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('number', TextType::class, [
                'label' => 'owl.ui.number',
                'required' => true,
                'constraints' => [
                    new GreaterThan(0),
                    new NotBlank(),
                ],
            ])
            ->add('fullNumber', TextType::class, [
                'label' => 'owl.ui.full_number',
                'required' => true,
                'constraints' => [
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        $data = $context->getRoot()->getData();

                        if (empty($data['serie']) && empty($value)) {
                            $context
                                ->buildViolation('owl.common.not_blank')
                                ->addViolation()
                            ;
                        }
                    }),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'series' => []
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice_numbering';
    }

    private function getSeriesChoices(array $series)
    {
        $options = [];

        foreach($series as $serie) {
            $options[$serie['format']] = $serie['id'];
        }

        $options['Turn off automatic numbering for this invoice'] = '';

        return $options;
    }
}
