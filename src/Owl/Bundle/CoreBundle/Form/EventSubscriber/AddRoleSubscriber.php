<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\EventSubscriber;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\Core\Model\RoleAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

final class AddRoleSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::SUBMIT => 'submit',
        ];
    }

    public function submit(FormEvent $event): void
    {
        /** @var (AdminUserInterface&RoleAwareInterface&UserInterface)|null $data */
        $data = $event->getData();
        $form = $event->getForm();

        Assert::isInstanceOf($data, RoleAwareInterface::class);
        Assert::isInstanceOf($data, AdminUserInterface::class);

        $roles = $data->getRoles();
        $roleRbac = $data->getRole();

        Assert::isInstanceOf($roleRbac, RoleInterface::class);

        if (!empty($roles)) {
            foreach ($roles as $role) {
                $data->removeRole($role);
            }
        }

        $canonicalName = $roleRbac->getCanonicalName();
        $data->addRole($canonicalName);

        $event->setData($data);
    }
}
