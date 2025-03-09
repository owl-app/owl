<?php

declare(strict_types=1);

namespace Owl\Component\Core\Resolver;

use Symfony\Component\Form\FormInterface;

interface RoleBasedValidationGroupResolverInterface
{
    public function __invoke(FormInterface $form): array;
}
