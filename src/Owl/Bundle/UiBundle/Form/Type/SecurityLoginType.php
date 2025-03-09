<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\UiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SecurityLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'owl.form.login.username',
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'owl.form.login.password',
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'owl.form.login.remember_me',
                'required' => false,
            ])
        ;
    }

    /**
     * @return 'sylius_security_login'
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_security_login';
    }
}
