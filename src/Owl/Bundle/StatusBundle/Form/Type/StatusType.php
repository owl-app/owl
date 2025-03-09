<?php

declare(strict_types=1);

namespace Owl\Bundle\StatusBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class StatusType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $resource = $builder->getData();

        $builder
            ->add('status', ChoiceType::class, [
                'choices' => array_flip($resource->getStatusesLabels()),
                'label' => 'owl.form.common.status',
                'multiple' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'owl.form.common.comment',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'rating_steps' => 5,
        ]);
    }

    /**
     * @return array<int<1, max>, int<1, max>>
     */
    private function createRatingList(int $maxRate): array
    {
        $ratings = [];
        for ($i = 1; $i <= $maxRate; ++$i) {
            $ratings[$i] = $i;
        }

        return $ratings;
    }
}
