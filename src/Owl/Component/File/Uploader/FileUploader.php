<?php

declare(strict_types=1);

namespace Owl\Component\File\Uploader;

use Gaufrette\Filesystem;
use Owl\Component\File\Generator\FilePathGeneratorInterface;
use Owl\Component\File\Generator\UploadedFilePathGenerator;
use Owl\Component\File\Model\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;

class FileUploader implements FileUploaderInterface
{
    /** @var Filesystem */
    protected $filesystem;

    /** @var FilePathGeneratorInterface */
    protected $imagePathGenerator;

    public function __construct(
        Filesystem $filesystem,
        ?FilePathGeneratorInterface $imagePathGenerator,
    ) {
        $this->filesystem = $filesystem;
        $this->imagePathGenerator = $imagePathGenerator ?? new UploadedFilePathGenerator();
    }

    public function upload(FileInterface $image): void
    {
        if (!$image->hasFile()) {
            return;
        }

        /** @var UploadedFile $file */
        $file = $image->getFile();

        Assert::isInstanceOf($file, UploadedFile::class);

        if (null !== $image->getPath() && $this->has($image->getPath())) {
            $this->remove($image->getPath());
        }

        do {
            $path = $this->imagePathGenerator->generate($image);
        } while ($this->isAdBlockingProne($path) || $this->filesystem->has($path));

        $image->setPath($path);
        $image->setName($file->getClientOriginalName());

        $this->filesystem->write(
            $image->getPath(),
            file_get_contents($image->getFile()->getPathname()),
        );
    }

    public function remove(string $path): bool
    {
        if ($this->filesystem->has($path)) {
            return $this->filesystem->delete($path);
        }

        return false;
    }

    private function has(string $path): bool
    {
        return $this->filesystem->has($path);
    }

    /**
     * Will return true if the path is prone to be blocked by ad blockers
     */
    private function isAdBlockingProne(string $path): bool
    {
        return strpos($path, 'ad') !== false;
    }
}
