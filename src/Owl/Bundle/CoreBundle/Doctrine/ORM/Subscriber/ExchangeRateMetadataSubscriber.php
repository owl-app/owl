<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Owl\Component\Core\Model\ExchangeRate;

class ExchangeRateMetadataSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [Events::loadClassMetadata];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $metadata = $args->getClassMetadata();

        if ($metadata->getName() === ExchangeRate::class) {
            if ($metadata->hasField('ratio')) {
                unset($metadata->fieldMappings['ratio']);
                unset($metadata->fieldNames['ratio']);
                unset($metadata->columnNames['ratio']);
            }

            $metadata->mapField([
                'fieldName' => 'ratio',
                'type' => 'decimal',
                'precision' => 20,
                'scale' => 10,
                'columnName' => 'ratio',
            ]);
        }
    }
}
