<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Controller;

use Exception;
use Owl\Bridge\SyliusResource\Controller\BaseController;
use Owl\Bundle\RbacBundle\Factory\PermissionFormFactoryInterface;
use Owl\Component\Rbac\Model\RoleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yiisoft\Rbac\ManagerInterface;

final class RoleController extends BaseController
{
    public function availablesAction(Request $request, PermissionFormFactoryInterface $permissionFormFactory, ManagerInterface $rbacManager): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var RoleInterface $resource */
        $resource = $this->findOr404($configuration);

        $this->isGrantedOr403($configuration, 'role_availabes', $resource);

        $forms = $permissionFormFactory->createByExists(
            $configuration,
            array_keys($rbacManager->getPermissionsByRoleName($resource->getName())),
        );

        return $this->render($configuration->getTemplate('index.html'), [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'role' => $resource,
            'forms' => $forms,
        ]);
    }

    public function assignAction(Request $request, ManagerInterface $rbacManager): Response
    {
        return $this->changePermission('add', $request, $rbacManager);
    }

    public function revokeAction(Request $request, ManagerInterface $rbacManager): Response
    {
        return $this->changePermission('remove', $request, $rbacManager);
    }

    /**
     * @return Response|null
     */
    private function changePermission(string $action, Request $request, ManagerInterface $rbacManager)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $formOptions = array_merge(
            $configuration->getFormOptions(),
            [
                'csrf_field_name' => '_csrf_token',
                'csrf_token_id' => $request->request->get('name'),
            ],
        );
        $method = $action === 'remove' ? 'DELETE' : 'POST';

        $this->isGrantedOr403($configuration, 'role_' . $action);

        /** @var RoleInterface $role */
        $role = $this->findOr404($configuration);

        $form = $this->container->get('form.factory')->createNamed('', $configuration->getFormType(), null, $formOptions);
        $form->handleRequest($request);

        if ($request->isMethod($method) && $form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $rbacManager->{$action . 'Child'}($role->getName(), $formData['name']);

                if (!$configuration->isHtmlRequest()) {
                    $responseData = [
                        'message' => $this->get('translator')->trans('owl.rbac.permission.add_success', [], 'flashes'),
                    ];

                    return $this->createRestView($configuration, $responseData, Response::HTTP_OK);
                }
            } catch(Exception $e) {
                $responseData = [
                    'message' => $e->getMessage(),
                ];

                return $this->createRestView($configuration, $responseData, Response::HTTP_BAD_REQUEST);
            }
        } else {
            $responseData = [
                'message' => [
                    'status' => 'error',
                    'errors' => $this->getErrorMessages($form),
                ],
            ];

            return $this->createRestView($configuration, $responseData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
