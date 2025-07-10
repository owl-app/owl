<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\EventSubscriber;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\AdminUserRegistrationDataInterface;
use Owl\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class AdminUserRegistrationFormSubscriber implements EventSubscriberInterface
{
    public function __construct(private string $classAdminUserRegistrationData, private GeneratorInterface $tokenGenerator)
    {
    }

    /**
     * @return array{'form.pre_submit': 'preSubmit'}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function preSubmit(FormEvent $event): void
    {
        /** @var array<string, mixed> $rawData */
        $rawData = $event->getData();
        $form = $event->getForm();
        $data = $form->getData();

        Assert::isInstanceOf($data, AdminUserInterface::class);

        $data->setRegistration($this->copyDataToRegistration($rawData));

        $token = $this->tokenGenerator->generate();
        $data->setEmailVerificationToken($token);
        $data->setDisplayName($rawData['firstName'] . ' ' . $rawData['lastName']);

        $form->setData($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function copyDataToRegistration(array $data): AdminUserRegistrationDataInterface
    {
        /** @var AdminUserRegistrationDataInterface $registration */
        $registration = new $this->classAdminUserRegistrationData();

        $registration->setFirstName((string) $data['firstName']);
        $registration->setLastName((string) $data['lastName']);
        $registration->setPhone((string) $data['phone']);
        $registration->setEmail((string) $data['email']);

        return $registration;
    }
}
