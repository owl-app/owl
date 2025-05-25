<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM;

use Owl\Component\Core\Doctrine\Persistence\RepositoryInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of InvoiceInterface
 */
class InvoiceRepository extends EntityRepository implements RepositoryInterface
{
    use RepositoryTrait;
}
