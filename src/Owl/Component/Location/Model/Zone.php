<?php

declare(strict_types=1);

namespace Owl\Component\Location\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Zone implements ZoneInterface, \Stringable
{
    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $type;

    /** @var Collection<array-key, ZoneMemberInterface> */
    protected $members;

    public function __construct()
    {
        /** @var ArrayCollection<array-key, ZoneMemberInterface> $this->members */
        $this->members = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public static function getTypes(): array
    {
        return [self::TYPE_COUNTRY, self::TYPE_PROVINCE, self::TYPE_ZONE];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function hasMembers(): bool
    {
        return !$this->members->isEmpty();
    }

    public function addMember(ZoneMemberInterface $member): void
    {
        if (!$this->hasMember($member)) {
            $this->members->add($member);
            $member->setBelongsTo($this);
        }
    }

    public function removeMember(ZoneMemberInterface $member): void
    {
        if ($this->hasMember($member)) {
            $this->members->removeElement($member);
            $member->setBelongsTo(null);
        }
    }

    public function hasMember(ZoneMemberInterface $member): bool
    {
        return $this->members->contains($member);
    }
}
