<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Owl\Bridge\SyliusResource\Doctrine\Orm\CollectionProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Owl\Bundle\CoreBundle\Entity\User;

final class UserChoiceType extends AbstractType
{
    /**
     * @param EntityRepository<User> $userRepository
     */
    public function __construct(
        private EntityRepository $userRepository,
        private CollectionProviderInterface $collectionProvider,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->collectionProvider->get($this->userRepository),
            'choice_value' => 'id',
            'choice_label' => 'displayName',
            'label' => false,
            'empty_data' => null,
            'placeholder' => 'owl.form.user.no_user',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_user_choice';
    }
}