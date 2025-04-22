<?php

declare(strict_types=1);

namespace Owl\Component\Location\Model;

use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;

interface ZoneMemberInterface extends ResourceInterface, CodeAwareInterface
{
    public function getBelongsTo(): ?ZoneInterface;

    public function setBelongsTo(?ZoneInterface $belongsTo): void;
}
