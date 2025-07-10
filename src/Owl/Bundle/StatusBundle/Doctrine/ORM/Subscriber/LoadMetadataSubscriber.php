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

namespace Owl\Bundle\StatusBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

final class LoadMetadataSubscriber implements EventSubscriber
{
    /** @var array<string, array{status: array{classes: array{model: string}}, subject: string, owner: string}> */
    private $subjects;

    /**
     * @param array<string, array{status: array{classes: array{model: string}}, subject: string, owner: string}> $subjects
     */
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
            if ($class['status']['classes']['model'] === $metadata->getName()) {
                $statusableEntity = $class['subject'];
                $ownerEntity = $class['owner'];
                $statusableEntityMetadata = $metadataFactory->getMetadataFor($statusableEntity);
                $ownerEntityMetadata = $metadataFactory->getMetadataFor($ownerEntity);

                $metadata->mapManyToOne($this->createSubjectMapping($statusableEntity, $subject, $statusableEntityMetadata));
                $metadata->mapManyToOne($this->createReviewerMapping($ownerEntity, $ownerEntityMetadata));
            }

            if ($class['subject'] === $metadata->getName()) {
                $statusEntity = $class['status']['classes']['model'];

                $metadata->mapOneToMany($this->createReviewsMapping($statusEntity));
            }
        }
    }

    /**
     * @return array{fieldName: 'statusSubject', targetEntity: string, inversedBy: 'statuses', joinColumns: list{array{name: string, referencedColumnName: string, nullable: false, onDelete: 'CASCADE'}}}
     */
    private function createSubjectMapping(
        string $statusableEntity,
        string $subject,
        ClassMetadata $statusableEntityMetadata,
    ): array {
        return [
            'fieldName' => 'statusSubject',
            'targetEntity' => $statusableEntity,
            'inversedBy' => 'statuses',
            'joinColumns' => [[
                'name' => $subject . '_id',
                'referencedColumnName' => $statusableEntityMetadata->fieldMappings['id']['columnName'] ?? $statusableEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];
    }

    /**
     * @return array{fieldName: 'owner', targetEntity: string, joinColumns: list{array{name: 'owner_id', referencedColumnName: string, nullable: false, onDelete: 'CASCADE'}}, cascade: list{'persist'}}
     */
    private function createReviewerMapping(string $ownerEntity, ClassMetadata $ownerEntityMetadata): array
    {
        return [
            'fieldName' => 'owner',
            'targetEntity' => $ownerEntity,
            'joinColumns' => [[
                'name' => 'owner_id',
                'referencedColumnName' => $ownerEntityMetadata->fieldMappings['id']['columnName'] ?? $ownerEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
            'cascade' => ['persist'],
        ];
    }

    /**
     * @return array{fieldName: 'statuses', targetEntity: string, mappedBy: 'statusSubject', cascade: list{'all'}}
     */
    private function createReviewsMapping(string $statusEntity): array
    {
        return [
            'fieldName' => 'statuses',
            'targetEntity' => $statusEntity,
            'mappedBy' => 'statusSubject',
            'cascade' => ['all'],
        ];
    }
}
