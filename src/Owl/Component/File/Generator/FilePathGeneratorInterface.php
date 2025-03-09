<?php

declare(strict_types=1);

namespace Owl\Component\File\Generator;

use Owl\Component\File\Model\FileInterface;

interface FilePathGeneratorInterface
{
    /**
     * It needs to return a different value on each call, so that consumers don't end up in an infinite loop.
     */
    public function generate(FileInterface $image): string;
}
