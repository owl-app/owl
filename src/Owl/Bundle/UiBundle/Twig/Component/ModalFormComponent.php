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

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ModalFormComponent
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp()]
    public bool $isOpen = false;

    #[LiveProp()]
    public ?string $resourceId = null;

    #[LiveAction]
    public function toggle(#[LiveArg()] ?string $resourceId): void
    {
        $this->isOpen = !$this->isOpen;

        $this->resourceId = $resourceId;

        if ($this->isOpen) {
            $this->dispatchBrowserEvent('owl_admin:modal:opened');
        }
    }
}
