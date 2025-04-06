<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RedirectResponseFactory implements RedirectResponseFactoryInterface
{
    public function __construct( 
        private RouterInterface $router,
    ) {
    }

    public function create(Request $request, string|array $redirectRoute): Response
    {
        $url = is_array($redirectRoute)
            ? $this->router->generate($redirectRoute['route'] ?? 'sylius_admin_login', $redirectRoute['params'] ?? [])
            : $this->router->generate($redirectRoute);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT, [
                'X-OWL-LOCATION' => $url,
            ]);
        }

        return new RedirectResponse($url);
    }
}
