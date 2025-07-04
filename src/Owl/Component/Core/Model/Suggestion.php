<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Owl\Component\File\Model\FileInterface;
use Owl\Component\Status\Model\StatusInterface;
use Owl\Component\Suggestion\Model\Suggestion as BaseSuggestion;
use Owl\Component\User\Model\UserInterface as BaseUserInterface;
use Owl\Component\Core\Model\AdminUserInterface;

class Suggestion extends BaseSuggestion implements SuggestionInterface
{
    /** @var AdminUserInterface|null */
    protected $user;

    /** @var Collection<array-key, FileInterface> */
    protected $files;

    /** @var Collection<array-key, StatusInterface> */
    protected $statuses;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, FileInterface> $this->files */
        $this->files = new ArrayCollection();
        /** @var ArrayCollection<array-key, StatusInterface> $this->statuses */
        $this->statuses = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->title;
    }

    /**
     * @return AdminUserInterface|null
     */
    public function getUser(): ?AdminUserInterface
    {
        return $this->user;
    }

    public function setUser(?BaseUserInterface $user): void
    {
        $this->user = $user;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(FileInterface $file): void
    {
        $this->files->add($file);
    }

    public function removeFile(FileInterface $file): void
    {
        $this->files->removeElement($file);
    }

    public function getStatuses(): Collection
    {
        return $this->statuses;
    }

    public function addStatus(StatusInterface $status): void
    {
        $this->statuses->add($status);
    }

    public function removeStatus(StatusInterface $status): void
    {
        $this->statuses->removeElement($status);
    }
}