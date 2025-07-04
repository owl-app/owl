<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\UiBundle\Twig\Component;

use Sylius\Resource\Model\ResourceInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent]
class ResourceModalFormComponent
{
    use LiveCollectionTrait;
    use TemplatePropTrait;

    /** @use ResourceModalFormComponentTrait<ResourceInterface> */
    use ResourceModalFormComponentTrait {
        initialize as public __construct;
    }
}
