<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\Tests\Doctrine\ORM\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Owl\Bundle\FileBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LoadMetadataSubscriberTest extends TestCase
{
    private LoadMetadataSubscriber $subscriber;
    private EntityManagerInterface&MockObject $entityManager;
    private ClassMetadataFactory&MockObject $metadataFactory;
    private ClassMetadata&MockObject $metadata;
    private LoadClassMetadataEventArgs&MockObject $eventArgs;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $this->metadata = $this->createMock(ClassMetadata::class);
        $this->eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);

        $this->eventArgs->method('getClassMetadata')
            ->willReturn($this->metadata);
        $this->eventArgs->method('getEntityManager')
            ->willReturn($this->entityManager);
        $this->entityManager->method('getMetadataFactory')
            ->willReturn($this->metadataFactory);
    }

    public function testReturnsSubscribedEvents(): void
    {
        // Arrange
        $this->subscriber = new LoadMetadataSubscriber([]);

        // Act
        $subscribedEvents = $this->subscriber->getSubscribedEvents();

        // Assert
        $this->assertSame(['loadClassMetadata'], $subscribedEvents);
    }

    public function testMapsFileEntityManyToOneAssociationsWhenFileModelMatches(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'file' => [
                    'classes' => [
                        'model' => 'App\Entity\ProductFile'
                    ]
                ],
                'subject' => 'App\Entity\Product',
                'uploader' => [
                    'classes' => [
                        'model' => 'App\Entity\User'
                    ]
                ]
            ]
        ];

        $this->subscriber = new LoadMetadataSubscriber($subjects);

        $fileableMetadata = $this->createMock(ClassMetadata::class);
        $uploaderMetadata = $this->createMock(ClassMetadata::class);

        $fileableMetadata->fieldMappings = ['id' => ['fieldName' => 'id']];
        $uploaderMetadata->fieldMappings = ['id' => ['fieldName' => 'id']];

        $this->metadata->method('getName')
            ->willReturn('App\Entity\ProductFile');

        $this->metadataFactory->method('getMetadataFor')
            ->willReturnMap([
                ['App\Entity\Product', $fileableMetadata],
                ['App\Entity\User', $uploaderMetadata]
            ]);

        $expectedSubjectMapping = [
            'fieldName' => 'fileSubject',
            'targetEntity' => 'App\Entity\Product',
            'inversedBy' => 'files',
            'joinColumns' => [[
                'name' => 'product_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $expectedUploaderMapping = [
            'fieldName' => 'author',
            'targetEntity' => 'App\Entity\User',
            'joinColumns' => [[
                'name' => 'author_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
            'cascade' => ['persist'],
        ];

        $this->metadata->expects($this->exactly(2))
            ->method('mapManyToOne')
            ->willReturnCallback(function ($mapping) use ($expectedSubjectMapping, $expectedUploaderMapping) {
                static $callCount = 0;
                $callCount++;
                
                if ($callCount === 1) {
                    $this->assertEquals($expectedSubjectMapping, $mapping);
                } elseif ($callCount === 2) {
                    $this->assertEquals($expectedUploaderMapping, $mapping);
                }
            });

        // Act
        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    public function testMapsSubjectEntityOneToManyAssociationWhenSubjectModelMatches(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'file' => [
                    'classes' => [
                        'model' => 'App\Entity\ProductFile'
                    ]
                ],
                'subject' => 'App\Entity\Product',
                'uploader' => [
                    'classes' => [
                        'model' => 'App\Entity\User'
                    ]
                ]
            ]
        ];

        $this->subscriber = new LoadMetadataSubscriber($subjects);

        $this->metadata->method('getName')
            ->willReturn('App\Entity\Product');

        $expectedMapping = [
            'fieldName' => 'files',
            'targetEntity' => 'App\Entity\ProductFile',
            'mappedBy' => 'fileSubject',
            'orderBy' => ['createdAt' => 'DESC'],
            'cascade' => ['all'],
        ];

        $this->metadata->expects($this->once())
            ->method('mapOneToMany')
            ->with($expectedMapping);

        // Act
        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    public function testUsesColumnNameWhenAvailableInFieldMapping(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'file' => [
                    'classes' => [
                        'model' => 'App\Entity\ProductFile'
                    ]
                ],
                'subject' => 'App\Entity\Product',
                'uploader' => [
                    'classes' => [
                        'model' => 'App\Entity\User'
                    ]
                ]
            ]
        ];

        $this->subscriber = new LoadMetadataSubscriber($subjects);

        $fileableMetadata = $this->createMock(ClassMetadata::class);
        $uploaderMetadata = $this->createMock(ClassMetadata::class);

        $fileableMetadata->fieldMappings = ['id' => ['fieldName' => 'id', 'columnName' => 'product_id']];
        $uploaderMetadata->fieldMappings = ['id' => ['fieldName' => 'id', 'columnName' => 'user_id']];

        $this->metadata->method('getName')
            ->willReturn('App\Entity\ProductFile');

        $this->metadataFactory->method('getMetadataFor')
            ->willReturnMap([
                ['App\Entity\Product', $fileableMetadata],
                ['App\Entity\User', $uploaderMetadata]
            ]);

        $expectedSubjectMapping = [
            'fieldName' => 'fileSubject',
            'targetEntity' => 'App\Entity\Product',
            'inversedBy' => 'files',
            'joinColumns' => [[
                'name' => 'product_id',
                'referencedColumnName' => 'product_id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $expectedUploaderMapping = [
            'fieldName' => 'author',
            'targetEntity' => 'App\Entity\User',
            'joinColumns' => [[
                'name' => 'author_id',
                'referencedColumnName' => 'user_id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
            'cascade' => ['persist'],
        ];

        $this->metadata->expects($this->exactly(2))
            ->method('mapManyToOne')
            ->willReturnCallback(function ($mapping) use ($expectedSubjectMapping, $expectedUploaderMapping) {
                static $callCount = 0;
                $callCount++;
                
                if ($callCount === 1) {
                    $this->assertEquals($expectedSubjectMapping, $mapping);
                } elseif ($callCount === 2) {
                    $this->assertEquals($expectedUploaderMapping, $mapping);
                }
            });

        // Act
        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    public function testProcessesMultipleSubjectsConfiguration(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'file' => [
                    'classes' => [
                        'model' => 'App\Entity\ProductFile'
                    ]
                ],
                'subject' => 'App\Entity\Product',
                'uploader' => [
                    'classes' => [
                        'model' => 'App\Entity\User'
                    ]
                ]
            ],
            'category' => [
                'file' => [
                    'classes' => [
                        'model' => 'App\Entity\CategoryFile'
                    ]
                ],
                'subject' => 'App\Entity\Category',
                'uploader' => [
                    'classes' => [
                        'model' => 'App\Entity\User'
                    ]
                ]
            ]
        ];

        $this->subscriber = new LoadMetadataSubscriber($subjects);

        $this->metadata->method('getName')
            ->willReturn('App\Entity\ProductFile');

        $fileableMetadata = $this->createMock(ClassMetadata::class);
        $uploaderMetadata = $this->createMock(ClassMetadata::class);

        $fileableMetadata->fieldMappings = ['id' => ['fieldName' => 'id']];
        $uploaderMetadata->fieldMappings = ['id' => ['fieldName' => 'id']];

        $this->metadataFactory->method('getMetadataFor')
            ->willReturnMap([
                ['App\Entity\Product', $fileableMetadata],
                ['App\Entity\User', $uploaderMetadata]
            ]);

        $this->metadata->expects($this->exactly(2))
            ->method('mapManyToOne');

        // Act
        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    public function testDoesNothingWhenNoSubjectsMatch(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'file' => [
                    'classes' => [
                        'model' => 'App\Entity\ProductFile'
                    ]
                ],
                'subject' => 'App\Entity\Product',
                'uploader' => [
                    'classes' => [
                        'model' => 'App\Entity\User'
                    ]
                ]
            ]
        ];

        $this->subscriber = new LoadMetadataSubscriber($subjects);

        $this->metadata->method('getName')
            ->willReturn('App\Entity\UnrelatedEntity');

        $this->metadata->expects($this->never())
            ->method('mapManyToOne');

        $this->metadata->expects($this->never())
            ->method('mapOneToMany');

        // Act
        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    public function testHandlesEmptySubjectsArray(): void
    {
        // Arrange
        $this->subscriber = new LoadMetadataSubscriber([]);

        $this->metadata->method('getName')
            ->willReturn('App\Entity\SomeEntity');

        $this->metadata->expects($this->never())
            ->method('mapManyToOne');

        $this->metadata->expects($this->never())
            ->method('mapOneToMany');

        // Act
        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    #[DataProvider('subjectMappingDataProvider')]
    public function testCreatesCorrectSubjectMappingForDifferentSubjects(
        string $subject,
        string $expectedJoinColumnName
    ): void {
        // Arrange
        $subjects = [
            $subject => [
                'file' => [
                    'classes' => [
                        'model' => 'App\Entity\TestFile'
                    ]
                ],
                'subject' => 'App\Entity\TestEntity',
                'uploader' => [
                    'classes' => [
                        'model' => 'App\Entity\User'
                    ]
                ]
            ]
        ];

        $this->subscriber = new LoadMetadataSubscriber($subjects);

        $fileableMetadata = $this->createMock(ClassMetadata::class);
        $uploaderMetadata = $this->createMock(ClassMetadata::class);

        $fileableMetadata->fieldMappings = ['id' => ['fieldName' => 'id']];
        $uploaderMetadata->fieldMappings = ['id' => ['fieldName' => 'id']];

        $this->metadata->method('getName')
            ->willReturn('App\Entity\TestFile');

        $this->metadataFactory->method('getMetadataFor')
            ->willReturnMap([
                ['App\Entity\TestEntity', $fileableMetadata],
                ['App\Entity\User', $uploaderMetadata]
            ]);

        $this->metadata->expects($this->exactly(2))
            ->method('mapManyToOne')
            ->willReturnCallback(function ($mapping) use ($expectedJoinColumnName) {
                static $callCount = 0;
                $callCount++;
                
                if ($callCount === 1) {
                    $this->assertSame($expectedJoinColumnName, $mapping['joinColumns'][0]['name']);
                }
            });

        // Act
        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    public function testMapsBothFileAndSubjectEntitiesForMatchingConfiguration(): void
    {
        // Arrange
        $subjects = [
            'product' => [
                'file' => [
                    'classes' => [
                        'model' => 'App\Entity\ProductFile'
                    ]
                ],
                'subject' => 'App\Entity\Product',
                'uploader' => [
                    'classes' => [
                        'model' => 'App\Entity\User'
                    ]
                ]
            ]
        ];

        $fileMetadata = $this->createMock(ClassMetadata::class);
        $subjectMetadata = $this->createMock(ClassMetadata::class);
        $fileableMetadata = $this->createMock(ClassMetadata::class);
        $uploaderMetadata = $this->createMock(ClassMetadata::class);

        $fileableMetadata->fieldMappings = ['id' => ['fieldName' => 'id']];
        $uploaderMetadata->fieldMappings = ['id' => ['fieldName' => 'id']];

        // First call for file entity
        $fileEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $fileEventArgs->method('getClassMetadata')->willReturn($fileMetadata);
        $fileEventArgs->method('getEntityManager')->willReturn($this->entityManager);
        
        $fileMetadata->method('getName')->willReturn('App\Entity\ProductFile');
        $fileMetadata->expects($this->exactly(2))->method('mapManyToOne');

        // Second call for subject entity
        $subjectEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $subjectEventArgs->method('getClassMetadata')->willReturn($subjectMetadata);
        $subjectEventArgs->method('getEntityManager')->willReturn($this->entityManager);
        
        $subjectMetadata->method('getName')->willReturn('App\Entity\Product');
        $subjectMetadata->expects($this->once())->method('mapOneToMany');

        $this->metadataFactory->method('getMetadataFor')
            ->willReturnMap([
                ['App\Entity\Product', $fileableMetadata],
                ['App\Entity\User', $uploaderMetadata]
            ]);

        $this->subscriber = new LoadMetadataSubscriber($subjects);

        // Act
        $this->subscriber->loadClassMetadata($fileEventArgs);
        $this->subscriber->loadClassMetadata($subjectEventArgs);
    }

    public static function subjectMappingDataProvider(): array
    {
        return [
            'product subject' => ['product', 'product_id'],
            'category subject' => ['category', 'category_id'],
            'user subject' => ['user', 'user_id'],
            'brand subject' => ['brand', 'brand_id'],
        ];
    }
}
