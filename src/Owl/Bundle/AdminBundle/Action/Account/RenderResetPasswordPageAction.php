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

namespace Owl\Bundle\AdminBundle\Action\Account;

use Owl\Bundle\AdminBundle\Form\Type\ResetPasswordType;
use Owl\Bundle\CoreBundle\Provider\FlashBagProvider;
use Owl\Component\Core\Factory\Http\RedirectResponseFactoryInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\User\Model\UserInterface;
use Owl\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class RenderResetPasswordPageAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private FormFactoryInterface $formFactory,
        private RequestStack $requestStack,
        private RedirectResponseFactoryInterface $redirecResponse,
        private Environment $twig,
        private string $tokenTtl,
    ) {
    }

    public function __invoke(Request $request, string $token): Response
    {
        /** @var AdminUserInterface|null $admin */
        $admin = $this->userRepository->findOneBy(['passwordResetToken' => $token]);
        if (null === $admin) {
            return $this->redirecResponse->create($request, 'owl_admin_login');
        }

        $lifetime = new \DateInterval($this->tokenTtl);

        if (!$admin->isPasswordRequestNonExpired($lifetime)) {
            return $this->handleExpiredPasswordRequest($request);
        }

        $form = $this->formFactory->create(ResetPasswordType::class);

        return new Response(
            $this->twig->render('@OwlAdmin/security/reset_password.html.twig', [
                'form' => $form->createView(),
            ]),
        );
    }

    private function handleExpiredPasswordRequest(Request $request): RedirectResponse
    {
        FlashBagProvider
            ::getFlashBag($this->requestStack)
            ->add('error', 'owl.admin.password_reset.token_expired')
        ;

        $attributes = $request->attributes->get('_sylius', []);
        $redirect = $attributes['redirect'] ?? 'owl_admin_login';

        return $this->redirecResponse->create($request, $redirect);
    }
}