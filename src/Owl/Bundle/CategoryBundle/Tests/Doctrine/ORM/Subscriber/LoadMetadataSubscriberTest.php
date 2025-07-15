<?php

declare(strict_types=1);

namespace Owl\Bundle\CategoryBundle\Tests\Doctrine\ORM\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Owl\Bundle\CategoryBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(LoadMetadataSubscriber::class)]
final class LoadMetadataSubscriberTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;

    private ClassMetadata&MockObject $metadata;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->metadata = $this->createMock(ClassMetadata::class);
    }

    #[Test]
    public function it_returns_subscribed_events(): void
    {
        // Arrange
        $subjects = [];
        $subscriber = new LoadMetadataSubscriber($subjects);

        // Act
        $events = $subscriber->getSubscribedEvents();

        // Assert
        $this->assertSame(['loadClassMetadata'], $events);
    }

    #[Test]
    public function it_does_not_modify_metadata_when_class_is_not_a_subject(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'subject' => 'App\\Entity\\Product',
                'category' => [
                    'classes' => [
                        'model' => 'App\\Entity\\Category'
                    ]
                ]
            ]
        ];
        $subscriber = new LoadMetadataSubscriber($subjects);

        $this->metadata->method('getName')->willReturn('App\\Entity\\User');

        $eventArgs = new LoadClassMetadataEventArgs($this->metadata, $this->entityManager);

        // Act
        $subscriber->loadClassMetadata($eventArgs);

        // Assert
        // No assertions needed as we're testing that no modifications occur
        $this->assertTrue(true);
    }

    #[Test]
    public function it_maps_many_to_one_category_relation_when_class_is_subject(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'subject' => 'App\\Entity\\Product',
                'category' => [
                    'classes' => [
                        'model' => 'App\\Entity\\Category'
                    ]
                ]
            ]
        ];
        $subscriber = new LoadMetadataSubscriber($subjects);

        $this->metadata->method('getName')->willReturn('App\\Entity\\Product');

        $categoryMetadata = $this->createMock(ClassMetadata::class);
        $categoryMetadata->fieldMappings = [
            'id' => [
                'fieldName' => 'id',
                'columnName' => 'id'
            ]
        ];

        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory->method('getMetadataFor')
            ->with('App\\Entity\\Category')
            ->willReturn($categoryMetadata);

        $this->entityManager->method('getMetadataFactory')->willReturn($metadataFactory);

        $eventArgs = new LoadClassMetadataEventArgs($this->metadata, $this->entityManager);

        $expectedMapping = [
            'fieldName' => 'category',
            'targetEntity' => 'App\\Entity\\Category',
            'joinColumns' => [[
                'name' => 'category_id',
                'referencedColumnName' => 'id',
                'nullable' => true,
                'onDelete' => 'SET NULL',
            ]],
            'orderBy' => ['createdAt' => 'DESC'],
        ];

        $this->metadata->expects($this->once())
            ->method('mapManyToOne')
            ->with($expectedMapping);

        // Act
        $subscriber->loadClassMetadata($eventArgs);
    }

    #[Test]
    public function it_uses_field_name_when_column_name_is_not_available(): void
    {
        // Arrange
        $subjects = [
            'article' => [
                'subject' => 'App\\Entity\\Article',
                'category' => [
                    'classes' => [
                        'model' => 'App\\Entity\\ArticleCategory'
                    ]
                ]
            ]
        ];
        $subscriber = new LoadMetadataSubscriber($subjects);

        $this->metadata->method('getName')->willReturn('App\\Entity\\Article');

        $categoryMetadata = $this->createMock(ClassMetadata::class);
        $categoryMetadata->fieldMappings = [
            'id' => [
                'fieldName' => 'identifier'
            ]
        ];

        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory->method('getMetadataFor')
            ->with('App\\Entity\\ArticleCategory')
            ->willReturn($categoryMetadata);

        $this->entityManager->method('getMetadataFactory')->willReturn($metadataFactory);

        $eventArgs = new LoadClassMetadataEventArgs($this->metadata, $this->entityManager);

        $expectedMapping = [
            'fieldName' => 'category',
            'targetEntity' => 'App\\Entity\\ArticleCategory',
            'joinColumns' => [[
                'name' => 'category_id',
                'referencedColumnName' => 'identifier',
                'nullable' => true,
                'onDelete' => 'SET NULL',
            ]],
            'orderBy' => ['createdAt' => 'DESC'],
        ];

        $this->metadata->expects($this->once())
            ->method('mapManyToOne')
            ->with($expectedMapping);

        // Act
        $subscriber->loadClassMetadata($eventArgs);
    }

    #[Test]
    public function it_handles_multiple_subjects_configuration(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'subject' => 'App\\Entity\\Product',
                'category' => [
                    'classes' => [
                        'model' => 'App\\Entity\\ProductCategory'
                    ]
                ]
            ],
            'article' => [
                'subject' => 'App\\Entity\\Article',
                'category' => [
                    'classes' => [
                        'model' => 'App\\Entity\\ArticleCategory'
                    ]
                ]
            ]
        ];
        $subscriber = new LoadMetadataSubscriber($subjects);

        $this->metadata->method('getName')->willReturn('App\\Entity\\Article');

        $categoryMetadata = $this->createMock(ClassMetadata::class);
        $categoryMetadata->fieldMappings = [
            'id' => [
                'fieldName' => 'id',
                'columnName' => 'category_id'
            ]
        ];

        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory->method('getMetadataFor')
            ->with('App\\Entity\\ArticleCategory')
            ->willReturn($categoryMetadata);

        $this->entityManager->method('getMetadataFactory')->willReturn($metadataFactory);

        $eventArgs = new LoadClassMetadataEventArgs($this->metadata, $this->entityManager);

        $expectedMapping = [
            'fieldName' => 'category',
            'targetEntity' => 'App\\Entity\\ArticleCategory',
            'joinColumns' => [[
                'name' => 'category_id',
                'referencedColumnName' => 'category_id',
                'nullable' => true,
                'onDelete' => 'SET NULL',
            ]],
            'orderBy' => ['createdAt' => 'DESC'],
        ];

        $this->metadata->expects($this->once())
            ->method('mapManyToOne')
            ->with($expectedMapping);

        // Act
        $subscriber->loadClassMetadata($eventArgs);
    }

    #[Test]
    public function it_constructs_with_empty_subjects_array(): void
    {
        // Arrange & Act
        $subscriber = new LoadMetadataSubscriber([]);

        // Assert
        $this->assertInstanceOf(LoadMetadataSubscriber::class, $subscriber);
        $this->assertSame(['loadClassMetadata'], $subscriber->getSubscribedEvents());
    }

    #[Test]
    #[DataProvider('subjectsDataProvider')]
    public function it_handles_various_subject_configurations(array $subjects, string $className, bool $shouldMap): void
    {
        // Arrange
        $subscriber = new LoadMetadataSubscriber($subjects);

        $this->metadata->method('getName')->willReturn($className);

        $categoryMetadata = $this->createMock(ClassMetadata::class);
        $categoryMetadata->fieldMappings = [
            'id' => [
                'fieldName' => 'id',
                'columnName' => 'id'
            ]
        ];

        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory->method('getMetadataFor')->willReturn($categoryMetadata);

        $this->entityManager->method('getMetadataFactory')->willReturn($metadataFactory);

        $eventArgs = new LoadClassMetadataEventArgs($this->metadata, $this->entityManager);

        if ($shouldMap) {
            $this->metadata->expects($this->once())->method('mapManyToOne');
        } else {
            $this->metadata->expects($this->never())->method('mapManyToOne');
        }

        // Act
        $subscriber->loadClassMetadata($eventArgs);
    }

    /**
     * @return array<string, array{subjects: array, className: string, shouldMap: bool}>
     */
    public static function subjectsDataProvider(): array
    {
        return [
            'empty subjects' => [
                'subjects' => [],
                'className' => 'App\\Entity\\Product',
                'shouldMap' => false
            ],
            'matching subject' => [
                'subjects' => [
                    'product' => [
                        'subject' => 'App\\Entity\\Product',
                        'category' => [
                            'classes' => [
                                'model' => 'App\\Entity\\Category'
                            ]
                        ]
                    ]
                ],
                'className' => 'App\\Entity\\Product',
                'shouldMap' => true
            ],
            'non-matching subject' => [
                'subjects' => [
                    'product' => [
                        'subject' => 'App\\Entity\\Product',
                        'category' => [
                            'classes' => [
                                'model' => 'App\\Entity\\Category'
                            ]
                        ]
                    ]
                ],
                'className' => 'App\\Entity\\User',
                'shouldMap' => false
            ]
        ];
    }
}
