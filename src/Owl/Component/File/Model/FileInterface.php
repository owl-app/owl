<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Component\File\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface FileInterface extends TimestampableInterface, ResourceInterface
{
    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getFile(): ?\SplFileInfo;

    public function setFile(?\SplFileInfo $file): void;

    public function hasFile(): bool;

    public function getPath(): ?string;

    public function setPath(?string $path): void;

    public function hasPath(): bool;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getAuthor(): ?UploaderInterface;

    public function setAuthor(?UploaderInterface $author): void;

    public function getFileSubject(): ?FileableInterface;

    public function setFileSubject(?FileableInterface $fileSubject): void;
}
