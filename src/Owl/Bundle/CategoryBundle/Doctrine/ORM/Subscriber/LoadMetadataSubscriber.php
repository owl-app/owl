<?php

declare(strict_types=1);

namespace Owl\Bundle\CategoryBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

final class LoadMetadataSubscriber implements EventSubscriber
{
    /** @var array<string, array{subject: string, category: array{classes: array{model: string}}}> */
    private $subjects;

    /**
     * @param array<string, array{subject: string, category: array{classes: array{model: string}}}> $subjects
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
            if ($class['subject'] === $metadata->getName()) {
                $categoryEntity = $class['category']['classes']['model'];
                $categoryEntityMetadata = $metadataFactory->getMetadataFor($categoryEntity);

                $metadata->mapManyToOne($this->createCategoriesMapping($categoryEntity, $categoryEntityMetadata));
            }
        }
    }

    /**
     * @return array{fieldName: 'category', targetEntity: string, joinColumns: list{array{name: 'category_id', referencedColumnName: string, nullable: true, onDelete: 'SET NULL'}}, orderBy: array{createdAt: 'DESC'}}
     */
    private function createCategoriesMapping(
        string $categoryEntity,
        ClassMetadata $categoryEntityMetadata,
    ): array {
        return [
            'fieldName' => 'category',
            'targetEntity' => $categoryEntity,
            'joinColumns' => [[
                'name' => 'category_id',
                'referencedColumnName' => $categoryEntityMetadata->fieldMappings['id']['columnName'] ?? $categoryEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => true,
                'onDelete' => 'SET NULL',
            ]],
            'orderBy' => ['createdAt' => 'DESC'],
        ];
    }
}
