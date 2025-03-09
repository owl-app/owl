<?php

declare(strict_types=1);

namespace spec\Owl\Bundle\CategoryBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class LoadMetadataSubscriberSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'categoryable' => [
                'subject' => 'AcmeBundle\Entity\CategoryableModel',
                'category' => [
                    'classes' => [
                        'model' => 'AcmeBundle\Entity\CategoryModel',
                    ],
                ],
            ],
        ]);
    }

    function it_implements_event_subscriber(): void
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_has_subscribed_events(): void
    {
        $this->getSubscribedEvents()->shouldReturn(['loadClassMetadata']);
    }

    function it_maps_proper_relations_for_categoryable_model(
        ClassMetadataFactory $metadataFactory,
        ClassMetadata $classMetadataInfo,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        LoadClassMetadataEventArgs $eventArguments,
    ): void {
        $eventArguments->getClassMetadata()->willReturn($metadata);
        $eventArguments->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($metadataFactory);

        $classMetadataInfo->fieldMappings = ['id' => ['columnName' => 'id']];
        $metadataFactory->getMetadataFor('AcmeBundle\Entity\CategoryModel')->willReturn($classMetadataInfo);
        $metadata->getName()->willReturn('AcmeBundle\Entity\CategoryableModel');

        $metadata->mapManyToOne([
            'fieldName' => 'category',
            'targetEntity' => 'AcmeBundle\Entity\CategoryModel',
            'joinColumns' => [[
                'name' => 'category_id',
                'referencedColumnName' => 'id',
                'nullable' => true,
                'onDelete' => 'SET NULL',
            ]],
            'orderBy' => ['createdAt' => 'DESC'],
        ])->shouldBeCalled();

        $this->loadClassMetadata($eventArguments);
    }

    function it_skips_mapping_configuration_if_metadata_name_is_not_different(
        ClassMetadataFactory $metadataFactory,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        LoadClassMetadataEventArgs $eventArguments,
    ): void {
        $this->beConstructedWith([
            'categoryable' => [
                'subject' => 'AcmeBundle\Entity\BadCategoryableModel',
                'category' => [
                    'classes' => [
                        'model' => 'AcmeBundle\Entity\CategoryModel',
                    ],
                ],
            ],
        ]);

        $eventArguments->getClassMetadata()->willReturn($metadata);
        $eventArguments->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($metadataFactory);
        $metadata->getName()->willReturn('AcmeBundle\Entity\CategoryableModel');

        $metadata->mapManyToOne(Argument::type('array'))->shouldNotBeCalled();

        $this->loadClassMetadata($eventArguments);
    }
}
