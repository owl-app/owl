<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

final class LoadMetadataSubscriber implements EventSubscriber
{
    /** @var array */
    private $subjects;

    public function __construct(array $subjects)
    {
        $this->subjects = $subjects;
    }

    /**
     * @return list{'loadClassMetadata'}
     */
    public function getSubscribedEvents(): array
    {
        return [
            'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments): void
    {
        $metadata = $eventArguments->getClassMetadata();

        $metadataFactory = $eventArguments->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['file']['classes']['model'] === $metadata->getName()) {
                $fileableEntity = $class['subject'];
                $uploaderEntity = $class['uploader']['classes']['model'];
                $fileableEntityMetadata = $metadataFactory->getMetadataFor($fileableEntity);
                $uploaderEntityMetadata = $metadataFactory->getMetadataFor($uploaderEntity);

                $metadata->mapManyToOne($this->createSubjectMapping($fileableEntity, $subject, $fileableEntityMetadata));
                $metadata->mapManyToOne($this->createUploaderMapping($uploaderEntity, $uploaderEntityMetadata));
            }

            if ($class['subject'] === $metadata->getName()) {
                $reviewEntity = $class['file']['classes']['model'];

                $metadata->mapOneToMany($this->createReviewsMapping($reviewEntity));
            }
        }
    }

    /**
     * @return array{fieldName: 'fileSubject', targetEntity: string, inversedBy: 'files', joinColumns: list{array{name: string, referencedColumnName: string, nullable: false, onDelete: 'CASCADE'}}}
     */
    private function createSubjectMapping(
        string $fileableEntity,
        string $subject,
        ClassMetadata $fileableEntityMetadata,
    ): array {
        return [
            'fieldName' => 'fileSubject',
            'targetEntity' => $fileableEntity,
            'inversedBy' => 'files',
            'joinColumns' => [[
                'name' => $subject . '_id',
                'referencedColumnName' => $fileableEntityMetadata->fieldMappings['id']['columnName'] ?? $fileableEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];
    }

    /**
     * @return array{fieldName: 'author', targetEntity: string, joinColumns: list{array{name: 'author_id', referencedColumnName: string, nullable: false, onDelete: 'CASCADE'}}, cascade: list{'persist'}}
     */
    private function createUploaderMapping(string $uploaderEntity, ClassMetadata $uploaderEntityMetadata): array
    {
        return [
            'fieldName' => 'author',
            'targetEntity' => $uploaderEntity,
            'joinColumns' => [[
                'name' => 'author_id',
                'referencedColumnName' => $uploaderEntityMetadata->fieldMappings['id']['columnName'] ?? $uploaderEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
            'cascade' => ['persist'],
        ];
    }

    /**
     * @return array{fieldName: 'files', targetEntity: string, mappedBy: 'fileSubject', orderBy: array{createdAt: 'DESC'}, cascade: list{'all'}}
     */
    private function createReviewsMapping(string $fileEntity): array
    {
        return [
            'fieldName' => 'files',
            'targetEntity' => $fileEntity,
            'mappedBy' => 'fileSubject',
            'orderBy' => ['createdAt' => 'DESC'],
            'cascade' => ['all'],
        ];
    }
}
