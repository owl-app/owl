<?php

declare(strict_types=1);

namespace Owl\Bundle\StatusBundle\Form\Type;

use Owl\Component\Status\Model\StatusableInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class StatusType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ResourceInterface|StatusableInterface|null $resource */
        $resource = $builder->getData();

        $choices = [];
        if ($resource instanceof StatusableInterface) {
            if (method_exists($resource, 'getStatusLabels')) {
                /** @var array<string, string> $labels */
                $labels = $resource->getStatusLabels();
                $choices = array_flip($labels);
            }
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
