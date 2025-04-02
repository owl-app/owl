<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Controller;

use Exception;
use Owl\Bundle\CoreBundle\Rbac\DirectPermissionUserProviderInterface;
use Owl\Bundle\RbacBundle\Factory\PermissionFormFactoryInterface;
use Owl\Bundle\UserBundle\Controller\UserController as BaseUserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ManagerInterface;

class UserController extends BaseUserController
{
    public function availablesAction(Request $request, PermissionFormFactoryInterface $permissionFormFactory, ManagerInterface $rbacManager): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, 'availables');

        $resource = $this->findOr404($configuration);
        $allPermissions = $rbacManager->getItemsByUserId($resource->getId());

        $permissionsFromRole = [];

        foreach ($rbacManager->getItemsByUserId($resource->getId()) as $item) {
            if ($item->getType() === Item::TYPE_ROLE) {
                $permissionsFromRole = array_merge($permissionsFromRole, $rbacManager->getPermissionsByRoleName($item->getName()));
            }
        }

        $forms = $permissionFormFactory->createByExists(
            $configuration,
            array_keys($allPermissions),
            array_keys($permissionsFromRole),
            true,
        );

        return $this->render($configuration->getTemplate('index.html'), [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'user' => $resource,
            'forms' => $forms,
        ]);
    }

    public function assignAction(
        Request $request,
        ManagerInterface $rbacManager,
        DirectPermissionUserProviderInterface $directPermissionUserProvider
    ): Response {
        return $this->changePermission('assign', $request, $rbacManager, $directPermissionUserProvider);
    }

    public function revokeAction(
        Request $request,
        ManagerInterface $rbacManager,
        DirectPermissionUserProviderInterface $directPermissionUserProvider
    ): Response {
        return $this->changePermission('revoke', $request, $rbacManager, $directPermissionUserProvider);
    }

    /**
     * @return Response|null
     */
    private function changePermission(
        string $action,
        Request $request,
        ManagerInterface $rbacManager,
        DirectPermissionUserProviderInterface $directPermissionUserProvider
    ): ?Response {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $formOptions = array_merge(
            $configuration->getFormOptions(),
            [
                'csrf_field_name' => '_csrf_token',
                'csrf_token_id' => $request->request->get('name'),
            ],
        );
        $method = $action === 'revoke' ? 'DELETE' : 'POST';

        $this->isGrantedOr403($configuration, $action);
        $user = $this->findOr404($configuration);

        $form = $this->container->get('form.factory')->createNamed('', $configuration->getFormType(), null, $formOptions);
        $form->handleRequest($request);

        if ($request->isMethod($method) && $form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $rbacManager->{$action}($formData['name'], $user->getId());

                if (!$configuration->isHtmlRequest()) {
                    $allPermissions = $directPermissionUserProvider->getPermission($user->getId());
                    $permissionsFromRole = [];

                    foreach ($rbacManager->getItemsByUserId($user->getId()) as $item) {
                        if ($item->getType() === Item::TYPE_ROLE) {
                            $permissionsFromRole = array_merge($permissionsFromRole, $rbacManager->getPermissionsByRoleName($item->getName()));
                        }
                    }

                    $responseData = [
                        'message' => $this->get('translator')->trans('owl.rbac.permission.add_success', [], 'flashes'),
                        'permissions' => [
                            'direct' => array_keys($allPermissions),
                            'inherited' => array_keys($permissionsFromRole),
                        ],
                    ];

                    return $this->createRestView($configuration, $responseData, Response::HTTP_OK);
                }
            } catch (Exception $e) {
                $responseData = [
                    'message' => $e->getMessage(),
                ];

                return $this->createRestView($configuration, $responseData, Response::HTTP_BAD_REQUEST);
            }
        }

        $responseData = [
            'status' => 'error',
            'errors' => $this->getErrorMessages($form),
        ];

        return $this->createRestView($configuration, $responseData, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
