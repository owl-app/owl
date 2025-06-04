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

use Owl\Bundle\AdminBundle\Form\Model\PasswordReset;
use Owl\Bundle\AdminBundle\Form\Type\ResetPasswordType;
use Owl\Bundle\CoreBundle\Command\Admin\Account\ResetPassword;
use Owl\Bundle\CoreBundle\Extractor\FormErrorExtractor;
use Owl\Bundle\CoreBundle\Provider\FlashBagProvider;
use Owl\Component\Core\Factory\Http\RedirectResponseFactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

final class ResetPasswordAction
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private MessageBusInterface $messageBus,
        private RequestStack $requestStack,
        private RedirectResponseFactoryInterface $redirecResponse,
        private Environment $twig,
    ) {
    }

    public function __invoke(Request $request, string $token): Response
    {
        $form = $this->formFactory->create(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PasswordReset $passwordReset */
            $passwordReset = $form->getData();

            $this->messageBus->dispatch(new ResetPassword($token, $passwordReset->getPassword()));

            FlashBagProvider
                ::getFlashBag($this->requestStack)
                ->add('success', 'owl.admin.password_reset.success')
            ;

            $attributes = $request->attributes->get('_sylius', []);
            $redirect = $attributes['redirect'] ?? 'owl_admin_login';

            return $this->redirecResponse->create($request, $redirect);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => FormErrorExtractor::extractErrors($form),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new Response(
            $this->twig->render('@OwlAdmin/security/reset_password.html.twig', [
                'form' => $form->createView(),
            ]),
        );
    }
}
