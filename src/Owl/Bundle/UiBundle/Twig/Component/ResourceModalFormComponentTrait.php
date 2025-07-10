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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;

/** @template T of ResourceInterface */
trait ResourceModalFormComponentTrait
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentToolsTrait;

    /** @var T|null */
    #[LiveProp(hydrateWith: 'hydrateResource', dehydrateWith: 'dehydrateResource')]
    public ?ResourceInterface $resource = null;

    /** @var RepositoryInterface<T> */
    protected RepositoryInterface $repository;

    protected FormFactoryInterface $formFactory;

    /** @var class-string */
    protected string $resourceClass;

    /** @var class-string */
    protected string $formClass;

    /**
     * @param RepositoryInterface<T> $repository
     *
     * @phpstan-return void
     */
    protected function initialize(
        RepositoryInterface $repository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
    ) {
        $this->repository = $repository;
        $this->formFactory = $formFactory;
        $this->resourceClass = $resourceClass;
        $this->formClass = $formClass;
    }

    /** @return T|null */
    public function hydrateResource(mixed $value): ?ResourceInterface
    {
        if (empty($value)) {
            return $this->createResource();
        }

        return $this->repository->find($value);
    }

    /** @param T|null $resource */
    public function dehydrateResource(?ResourceInterface $resource): mixed
    {
        return $resource?->getId();
    }

    /**
     * @param array<string, mixed> $props
     */
    #[PreMount]
    public function initializeResourceById(?array $props = []): void
    {
        if (!isset($props['resource'])) {
            $this->resource = isset($props['id']) ? $this->repository->find($props['id']) : $this->createResource();
        }
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->resource);
    }

    /** @return T */
    protected function createResource(): ResourceInterface
    {
        return new $this->resourceClass();
    }

    #[LiveListener('saved')]
    public function incrementProductCount(EntityManagerInterface $entityManager, Request $request): void
    {
        $this->submitForm();

        $post = $this->getForm()->getData();
        $post->setStatus('new');
        $entityManager->persist($post);
        $entityManager->flush();

        $this->dispatchBrowserEvent('owl_admin:modal:close');
    }
}
