<?php

declare(strict_types=1);

namespace Owl\Component\Location\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;

interface ZoneInterface extends ResourceInterface, CodeAwareInterface
{
    public const TYPE_COUNTRY = 'country';

    public const TYPE_PROVINCE = 'province';

    public const TYPE_ZONE = 'zone';

    /**
     * @return string[]
     */
    public static function getTypes(): array;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    /**
     * @return Collection<array-key, ZoneMemberInterface>
     */
    public function getMembers(): Collection;

    public function hasMembers(): bool;

    public function addMember(ZoneMemberInterface $member): void;

    public function removeMember(ZoneMemberInterface $member): void;

    public function hasMember(ZoneMemberInterface $member): bool;
}
