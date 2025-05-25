<?php

declare(strict_types=1);

namespace Owl\Component\File\Generator;

use Owl\Component\File\Model\FileInterface;

interface FilePathGeneratorInterface
{
    public function generate(FileInterface $image): string;
}
