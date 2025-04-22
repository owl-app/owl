<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Zone;

use Owl\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Owl\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Owl\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Owl\Component\Location\Model\ZoneInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
class FormComponent
{
    use LiveCollectionTrait;

    /** @use ResourceFormComponentTrait<ZoneInterface> */
    use ResourceFormComponentTrait {
        initialize as public __construct;
    }

    use TemplatePropTrait;

    #[LiveProp(fieldName: 'type')]
    public ?string $type = null;

    protected function instantiateForm(): FormInterface
    {
        $this->resource->setType($this->type);

        return $this->formFactory->create(
            $this->formClass,
            $this->resource,
            ['add_build_zone_form_subscriber' => false],
        );
    }
}
