<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\EventListener;

use Owl\Component\Location\Model\ZoneInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

/** @internal */
final class BuildZoneFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if (!isset($data['members'])) {
            return;
        }

        /** @var ZoneInterface $zone */
        $zone = $event->getForm()->getData();

        Assert::isInstanceOf($zone, ZoneInterface::class);

        $membersCodes = $zone->getMembers()
            ->map(fn ($member): string => $member->getCode())
            ->getValues()
        ;

        $members = [];
        $newlyAddedMembers = [];

        foreach ($data['members'] as $member) {
            if (!isset($member['code'])) {
                continue;
            }

            $existingMemberIndex = array_search($member['code'], $membersCodes, true);

            if (false === $existingMemberIndex) {
                $newlyAddedMembers[] = $member;

                continue;
            }

            $members[$existingMemberIndex] = $member;
        }

        array_push($members, ...$newlyAddedMembers);
        $data['members'] = $members;

        $event->setData($data);
    }
}
