<?php

declare(strict_types=1);

namespace Owl\Bundle\NotificationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class NotificationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'owl.form.common.subject',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'owl.form.common.description',
                'attr' => [
                    'class' => 'tinymce',
                    'data-theme' => 'advanced',
                ],
            ])
            ->add('dateIssue', DateType::class, [
                'label' => 'owl.form.common.date_issue',
                'widget' => 'single_text',
                'placeholder' => ['year' => '-', 'month' => '-', 'day' => '-'],
            ])
            ->add('currentFrom', DateType::class, [
                'label' => 'owl.form.notification.current_from',
                'widget' => 'single_text',
                'placeholder' => ['year' => '-', 'month' => '-', 'day' => '-'],
            ])
            ->add('currentTo', DateType::class, [
                'label' => 'owl.form.notification.current_to',
                'widget' => 'single_text',
                'placeholder' => ['year' => '-', 'month' => '-', 'day' => '-'],
            ])
        ;
    }

    /**
     * @return 'owl_notification'
     */
    public function getBlockPrefix(): string
    {
        return 'owl_notification';
    }
}
