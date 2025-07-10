<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RedirectResponseFactoryInterface
{
    /**
     * @param string|string[] $redirectRoute
     */
    public function create(Request $request, string|array $redirectRoute): Response;
}
