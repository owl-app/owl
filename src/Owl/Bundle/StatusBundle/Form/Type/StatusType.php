<?php

declare(strict_types=1);

namespace Owl\Bundle\StatusBundle\Form\Type;

use Owl\Component\Status\Model\Status;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class StatusType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Status|null $resource */
        $resource = $builder->getData();

        $choices = [];
        if ($resource instanceof Status) {
            $choices = array_flip($resource->getStatusesLabels());
        }

        $builder
            ->add('status', ChoiceType::class, [
                'choices' => $choices,
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
}
