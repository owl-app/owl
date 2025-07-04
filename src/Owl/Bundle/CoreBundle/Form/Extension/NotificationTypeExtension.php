<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Extension;

use Owl\Bundle\CoreBundle\Form\Type\GroupNotificationChoiceType;
use Owl\Bundle\NotificationBundle\Form\Type\NotificationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class NotificationTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('assignedGroup', GroupNotificationChoiceType::class, [
                'label' => 'owl.form.notification.group_assigned',
            ])
        ;
    }

    /**
     * @return class-string<NotificationType>
     */
    public function getExtendedType(): string
    {
        return NotificationType::class;
    }

    /**
     * @return iterable<class-string<NotificationType>>
     */
    public static function getExtendedTypes(): iterable
    {
        return [NotificationType::class];
    }
}