<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Owl\Bridge\SyliusResource\Doctrine\Orm\CollectionProviderInterface;
use Owl\Bundle\CoreBundle\Form\Type\UserChoiceType;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\Authorization\OwnerableUserInterface;
use Owl\Component\Core\Model\User\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class AddOwnerFormSubscriber implements EventSubscriberInterface
{
    /**
     * @param EntityRepository<UserInterface> $userRepository
     */
    public function __construct(
        private AdminUserContextInterface $adminUserContext,
        private EntityRepository $userRepository,
        private CollectionProviderInterface $collectionProvider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();
        $isAdminSystem = $this->adminUserContext->isAdminSystem();

        if ($data instanceof OwnerableUserInterface && $isAdminSystem) {
            $form
                ->add('user', UserChoiceType::class, [
                    'choices' => $this->collectionProvider->get(
                        $this->userRepository,
                        [],
                        [
                            'method' => 'findEnabledWithOwner',
                            'arguments' => [
                                'userId' => $data->getUser()?->getId(),
                            ],
                        ],
                    ),
                    'label' => 'owl.form.common.assign_user',
                ]);
        }
    }
}