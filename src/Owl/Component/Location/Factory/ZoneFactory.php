<?php

declare(strict_types=1);

namespace Owl\Component\Location\Factory;

use Owl\Component\Location\Model\ZoneInterface;
use Owl\Component\Location\Model\ZoneMemberInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of ZoneInterface
 *
 * @implements ZoneFactoryInterface<T>
 */
final class ZoneFactory implements ZoneFactoryInterface
{
    /**
     * @param FactoryInterface<T> $factory
     * @param FactoryInterface<ZoneMemberInterface> $zoneMemberFactory
     */
    public function __construct(
        private FactoryInterface $factory,
        private FactoryInterface $zoneMemberFactory,
    ) {
    }

    public function createNew(): ZoneInterface
    {
        return $this->factory->createNew();
    }

    public function createTyped(string $type): ZoneInterface
    {
        /** @var ZoneInterface $zone */
        $zone = $this->createNew();
        $zone->setType($type);

        return $zone;
    }

    public function createWithMembers(array $membersCodes): ZoneInterface
    {
        /** @var ZoneInterface $zone */
        $zone = $this->createNew();
        foreach ($membersCodes as $memberCode) {
            /** @var ZoneMemberInterface $zoneMember */
            $zoneMember = $this->zoneMemberFactory->createNew();
            $zoneMember->setCode($memberCode);

            $zone->addMember($zoneMember);
        }

        return $zone;
    }
}
