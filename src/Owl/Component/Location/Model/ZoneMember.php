<?php

declare(strict_types=1);

namespace Owl\Component\Location\Model;

class ZoneMember implements ZoneMemberInterface
{
    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var ZoneInterface|null */
    protected $belongsTo;

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

    public function getBelongsTo(): ?ZoneInterface
    {
        return $this->belongsTo;
    }

    public function setBelongsTo(?ZoneInterface $belongsTo): void
    {
        $this->belongsTo = $belongsTo;
    }
}
