<?php

declare(strict_types=1);

namespace Owl\Component\File\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class File implements FileInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string */
    protected $type;

    /** @var \SplFileInfo|null */
    protected $file;

    /** @var string|null */
    protected $path;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var UploaderInterface */
    protected $author;

    /** @var FileableInterface */
    protected $fileSubject;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getFile(): ?\SplFileInfo
    {
        return $this->file;
    }

    public function setFile(?\SplFileInfo $file): void
    {
        $this->file = $file;
    }

    public function hasFile(): bool
    {
        return null !== $this->file;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function hasPath(): bool
    {
        return null !== $this->path;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return UploaderInterface
     */
    public function getAuthor(): ?UploaderInterface
    {
        return $this->author;
    }

    public function setAuthor(?UploaderInterface $author): void
    {
        $this->author = $author;
    }

    /**
     * @return FileableInterface
     */
    public function getFileSubject(): ?FileableInterface
    {
        return $this->fileSubject;
    }

    public function setFileSubject(?FileableInterface $fileSubject): void
    {
        $this->fileSubject = $fileSubject;
    }
}
