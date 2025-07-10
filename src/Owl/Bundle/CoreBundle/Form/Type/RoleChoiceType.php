<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Type;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\Core\Repository\RoleRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RoleChoiceType extends AbstractType
{
    public function __construct(private RoleRepositoryInterface $roleRepository, private AdminUserContextInterface $adminUserContext)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getOptions(),
            'choice_label' => fn (RoleInterface $role): string => $role->getSetting()?->getDisplayName() ?? '',
            'choice_value' => 'id',
            'label' => false,
            'placeholder' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_role_choice';
    }

    /**
     * @return RoleInterface[]
     */
    private function getOptions(): array
    {
        if (!$this->adminUserContext->isAdminSystem()) {
            /** @var RoleInterface[] $roles */
            $roles = $this->roleRepository->findWithoutAdminSystem();

            return $roles;
        }

        /** @var RoleInterface[] $roles */
        $roles = $this->roleRepository->findAll();

        return $roles;
    }
}
