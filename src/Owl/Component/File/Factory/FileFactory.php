<?php

declare(strict_types=1);

namespace Owl\Component\File\Factory;

use Owl\Bridge\SyliusResource\Factory\Resource\ParentableFactory;
use Owl\Component\File\Model\FileableInterface;
use Owl\Component\File\Model\FileInterface;
use Owl\Component\File\Model\UploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of FileInterface
 *
 * @extends ParentableFactory<T>
 *
 * @implements FileFactoryInterface<T>
 */
final class FileFactory extends ParentableFactory implements FileFactoryInterface
{
    /** @var FactoryInterface */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createNew(): FileInterface
    {
        return $this->factory->createNew();
    }

    public function createForParent(string $parentName): FileInterface
    {
        /** @var FileableInterface $resourceParent */
        $resourceParent = $this->getResourceParents($parentName);

        /** @var FileInterface $file */
        $file = $this->factory->createNew();

        $file->setFileSubject($resourceParent);

        return $file;
    }

    public function createForSubjectWithUploader(string $parentName, ?UploaderInterface $reviewer): FileInterface
    {
        /** @var FileInterface $file */
        $file = $this->createForParent($parentName);
        $file->setAuthor($reviewer);

        return $file;
    }
}
