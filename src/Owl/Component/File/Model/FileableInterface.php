<?php

declare(strict_types=1);

namespace Owl\Component\File\Model;

use Doctrine\Common\Collections\Collection;

interface FileableInterface
{
    /**
     * @return Collection<array-key, FileInterface>
     */
    public function getFiles(): Collection;

    public function addFile(FileInterface $file): void;

    public function removeFile(FileInterface $file): void;
}
