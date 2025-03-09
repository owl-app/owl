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
        $this->roleRepository = $roleRepository;
        $this->adminUserContext = $adminUserContext;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getOptions(),
            'choice_label' => fn (RoleInterface $role): string => $role->getSetting()->getDisplayName(),
            'choice_value' => 'id',
            'label' => false,
            'placeholder' => false,
        ]);
    }

    /**
     * @return ChoiceType::class
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * @return 'owl_role_choice'
     */
    public function getBlockPrefix(): string
    {
        return 'owl_role_choice';
    }

    private function getOptions(): array
    {
        if (!$this->adminUserContext->isAdminSystem()) {
            return $this->roleRepository->findWithoutAdminSystem();
        }

        return $this->roleRepository->findAll();
    }
}
