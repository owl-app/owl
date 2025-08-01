<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Tests\Factory;

use Owl\Bundle\RbacBundle\Factory\PermissionFormFactory;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\Rbac\Factory\PermissionFactoryInterface;
use Owl\Component\Rbac\Model\AuthItemInterface;
use Owl\Component\Rbac\Provider\RoutesPermissionProviderInterface;
use Owl\Component\Rbac\Repository\PermissionRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class PermissionFormFactoryTest extends TestCase
{
    private PermissionFormFactory $factory;
    private PermissionFactoryInterface&MockObject $permissionFactory;
    private PermissionRepositoryInterface&MockObject $permissionRepository;
    private RepositoryInterface&MockObject $roleRepository;
    private RoutesPermissionProviderInterface&MockObject $routesPermissionProvider;
    private FormFactoryInterface&MockObject $formFactory;
    private RequestConfiguration&MockObject $requestConfiguration;

    protected function setUp(): void
    {
        $this->permissionFactory = $this->createMock(PermissionFactoryInterface::class);
        $this->permissionRepository = $this->createMock(PermissionRepositoryInterface::class);
        $this->roleRepository = $this->createMock(RepositoryInterface::class);
        $this->routesPermissionProvider = $this->createMock(RoutesPermissionProviderInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->requestConfiguration = $this->createMock(RequestConfiguration::class);

        $this->factory = new PermissionFormFactory(
            $this->permissionFactory,
            $this->permissionRepository,
            $this->roleRepository,
            $this->routesPermissionProvider,
            $this->formFactory
        );
    }

    public function testCreateByRoutesReturnsFormsGroupedByPermissionGroup(): void
    {
        $routes = [
            'permission1' => ['group' => 'group1', 'description' => 'Description 1'],
            'permission2' => ['group' => 'group1', 'description' => 'Description 2'],
            'permission3' => ['group' => 'group2', 'description' => 'Description 3'],
        ];

        $existingPermissions = ['permission1'];

        $permission1 = $this->createMock(AuthItemInterface::class);
        $permission2 = $this->createMock(AuthItemInterface::class);
        $permission3 = $this->createMock(AuthItemInterface::class);

        $form1 = $this->createMock(FormInterface::class);
        $form2 = $this->createMock(FormInterface::class);
        $form3 = $this->createMock(FormInterface::class);

        $formView1 = $this->createMock(FormView::class);
        $formView2 = $this->createMock(FormView::class);
        $formView3 = $this->createMock(FormView::class);

        $this->routesPermissionProvider->method('getPermissions')->willReturn($routes);
        $this->permissionRepository->method('findAllNames')->willReturn($existingPermissions);

        $this->permissionFactory->method('createWithData')
            ->willReturnOnConsecutiveCalls($permission1, $permission2, $permission3);

        $this->requestConfiguration->method('getFormOptions')->willReturn([]);
        $this->requestConfiguration->method('getFormType')->willReturn('form_type');

        $this->formFactory->method('createNamed')
            ->willReturnOnConsecutiveCalls($form1, $form2, $form3);

        $form1->method('createView')->willReturn($formView1);
        $form2->method('createView')->willReturn($formView2);
        $form3->method('createView')->willReturn($formView3);

        $result = $this->factory->createByRoutes($this->requestConfiguration);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('group1', $result);
        $this->assertArrayHasKey('group2', $result);
        $this->assertCount(2, $result['group1']);
        $this->assertCount(1, $result['group2']);
        $this->assertContains($formView1, $result['group1']);
        $this->assertContains($formView2, $result['group1']);
        $this->assertContains($formView3, $result['group2']);
    }

    public function testCreateByExistsReturnsFormsGroupedByPermissionGroup(): void
    {
        $assignedPermissions = ['permission1', 'role1'];
        $disabledPermissions = ['permission2'];

        $permission1 = $this->createMock(AuthItemInterface::class);
        $permission2 = $this->createMock(AuthItemInterface::class);
        $role1 = $this->createMock(RoleInterface::class);

        $form1 = $this->createMock(FormInterface::class);
        $form2 = $this->createMock(FormInterface::class);
        $formRole = $this->createMock(FormInterface::class);

        $formView1 = $this->createMock(FormView::class);
        $formView2 = $this->createMock(FormView::class);
        $formViewRole = $this->createMock(FormView::class);

        $permission1->method('getName')->willReturn('permission1');
        $permission1->method('getGroupPermission')->willReturn('group1');
        $permission1->method('getDescription')->willReturn('Description 1');

        $permission2->method('getName')->willReturn('permission2');
        $permission2->method('getGroupPermission')->willReturn('group1');
        $permission2->method('getDescription')->willReturn('Description 2');

        $role1->method('getName')->willReturn('role1');
        $role1->method('getDescription')->willReturn('Role Description');

        $this->permissionRepository->method('findAll')->willReturn([$permission1, $permission2]);
        $this->roleRepository->method('findAll')->willReturn([$role1]);

        $this->requestConfiguration->method('getFormOptions')->willReturn([]);
        $this->requestConfiguration->method('getFormType')->willReturn('form_type');

        $this->formFactory->method('createNamed')
            ->willReturnOnConsecutiveCalls($formRole, $form1, $form2);

        $formRole->method('createView')->willReturn($formViewRole);
        $form1->method('createView')->willReturn($formView1);
        $form2->method('createView')->willReturn($formView2);

        $result = $this->factory->createByExists($this->requestConfiguration, $assignedPermissions, $disabledPermissions, true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('availables_roles', $result);
        $this->assertArrayHasKey('group1', $result);
        $this->assertCount(1, $result['availables_roles']);
        $this->assertCount(2, $result['group1']);
        $this->assertContains($formViewRole, $result['availables_roles']);
        $this->assertContains($formView1, $result['group1']);
        $this->assertContains($formView2, $result['group1']);
    }

    public function testCreateByExistsWithoutRolesSkipsRoleForms(): void
    {
        $assignedPermissions = ['permission1'];
        $permission1 = $this->createMock(AuthItemInterface::class);

        $permission1->method('getName')->willReturn('permission1');
        $permission1->method('getGroupPermission')->willReturn('group1');
        $permission1->method('getDescription')->willReturn('Description 1');

        $this->permissionRepository->method('findAll')->willReturn([$permission1]);

        $this->requestConfiguration->method('getFormOptions')->willReturn([]);
        $this->requestConfiguration->method('getFormType')->willReturn('form_type');

        $form1 = $this->createMock(FormInterface::class);
        $formView1 = $this->createMock(FormView::class);

        $this->formFactory->method('createNamed')
            ->willReturn($form1);

        $form1->method('createView')->willReturn($formView1);

        $result = $this->factory->createByExists($this->requestConfiguration, $assignedPermissions, [], false);

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('availables_roles', $result);
        $this->assertArrayHasKey('group1', $result);
        $this->assertCount(1, $result['group1']);
        $this->assertContains($formView1, $result['group1']);
    }
} 