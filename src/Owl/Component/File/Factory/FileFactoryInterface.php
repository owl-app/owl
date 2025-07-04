<?php

declare(strict_types=1);

namespace Owl\Component\File\Factory;

use Owl\Component\File\Model\FileInterface;
use Owl\Component\File\Model\UploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface FileFactoryInterface extends FactoryInterface
{
    public function createForSubjectWithUploader(string $parentName, ?UploaderInterface $reviewer): FileInterface;
}