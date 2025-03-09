<?php

declare(strict_types=1);

namespace Owl\Component\File\Uploader;

use Owl\Component\File\Model\FileInterface;

interface FileUploaderInterface
{
    public function upload(FileInterface $image): void;

    public function remove(string $path): bool;
}
