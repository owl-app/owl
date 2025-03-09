<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Controller\Action;

use FOS\RestBundle\View\View;
use Owl\Bridge\SyliusResource\Controller\AbstractResourceAction;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Factory\NotificationAcceptedFactoryInterface;
use Owl\Component\Core\Repository\NotificationRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @property NotificationRepositoryInterface $repository
 */
final class AcceptNotificationAction extends AbstractResourceAction
{
    public function __construct(
        private AdminUserContextInterface $adminUserContext,
        private RepositoryInterface $notificationAcceptedRepository,
        private NotificationAcceptedFactoryInterface $notificationAcceptedFactory,
        private RequestConfigurationFactoryInterface $requestConfigurationFactory,
        private ViewHandlerInterface $viewHandler,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $notification = $this->findOr404($request);
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$this->isCsrfTokenValid((string) $notification->getId(), $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        $newAcceptedNotification = $this->notificationAcceptedFactory->createAction($notification, $this->adminUserContext->getUser());

        $this->notificationAcceptedRepository->add($newAcceptedNotification);

        $view = View::create($notification, Response::HTTP_CREATED);

        return $this->viewHandler->handle($configuration, $view);
    }

    protected function findOr404(Request $request): ResourceInterface
    {
        $notification = $this->repository->findNotAccepted(
            (int) $request->attributes->get('id'),
            $this->adminUserContext->getUser(),
            $this->adminUserContext->getRoleCanonicalName(),
        );

        if (!$request->attributes->has('id') || null === $notification) {
            throw new NotFoundHttpException(sprintf('The "%s" has not been found', $this->metadata->getHumanizedName()));
        }

        return $notification;
    }
}
