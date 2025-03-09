<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\EventListener;

use Owl\Component\File\Model\FileInterface;
use Owl\Component\File\Uploader\FileUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class FileUploadListener
{
    private FileUploaderInterface $uploader;

    public function __construct(FileUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function uploadFile(GenericEvent $event): void
    {
        $resource = $event->getSubject();
        Assert::isInstanceOf($resource, FileInterface::class);

        if (null !== $resource->getFile()) {
            $this->uploader->upload($resource);
        }
    }
}
