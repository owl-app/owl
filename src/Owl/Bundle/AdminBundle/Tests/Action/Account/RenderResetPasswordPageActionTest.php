<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Action\Account;

use Owl\Bundle\AdminBundle\Action\Account\RenderResetPasswordPageAction;
use Owl\Bundle\AdminBundle\Form\Type\ResetPasswordType;
use Owl\Bundle\CoreBundle\Provider\FlashBagProvider;
use Owl\Component\Core\Factory\Http\RedirectResponseFactoryInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;

#[CoversClass(RenderResetPasswordPageAction::class)]
final class RenderResetPasswordPageActionTest extends TestCase
{
    private RenderResetPasswordPageAction $action;
    private UserRepositoryInterface&MockObject $userRepository;
    private FormFactoryInterface&MockObject $formFactory;
    private RequestStack&MockObject $requestStack;
    private RedirectResponseFactoryInterface&MockObject $redirectResponseFactory;
    private Environment&MockObject $twig;
    private AdminUserInterface&MockObject $adminUser;
    private FormInterface&MockObject $form;
    private Request&MockObject $request;
    private string $tokenTtl;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->redirectResponseFactory = $this->createMock(RedirectResponseFactoryInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->request = $this->createMock(Request::class);
        $this->tokenTtl = 'PT1H';

        $this->action = new RenderResetPasswordPageAction(
            $this->userRepository,
            $this->formFactory,
            $this->requestStack,
            $this->redirectResponseFactory,
            $this->twig,
            $this->tokenTtl
        );
    }

    #[Test]
    public function it_renders_reset_password_page_when_token_is_valid(): void
    {
        // Arrange
        $token = 'valid-token';
        $formView = $this->createMock(FormView::class);
        $expectedContent = '<html>reset password form</html>';

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->isInstanceOf(\DateInterval::class))
            ->willReturn(true);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/security/reset_password.html.twig', [
                'form' => $formView,
            ])
            ->willReturn($expectedContent);

        // Act
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame($expectedContent, $response->getContent());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    #[Test]
    public function it_redirects_to_login_when_token_is_invalid(): void
    {
        // Arrange
        $token = 'invalid-token';
        $redirectResponse = new Response();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn(null);

        $this->redirectResponseFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->request, 'owl_admin_login')
            ->willReturn($redirectResponse);

        // Act
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertSame($redirectResponse, $response);
    }

    #[Test]
    public function it_redirects_when_token_is_expired(): void
    {
        // Arrange
        $token = 'expired-token';
        $redirectResponse = new Response();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->isInstanceOf(\DateInterval::class))
            ->willReturn(false);

        $session = $this->createMock(Session::class);
        $flashBag = $this->createMock(FlashBagInterface::class);
        
        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($session);

        $session
            ->expects($this->once())
            ->method('getBag')
            ->with('flashes')
            ->willReturn($flashBag);

        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('error', 'owl.admin.password_reset.token_expired');

        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_sylius', [])
            ->willReturn([]);

        $this->request->attributes = $attributes;

        $this->redirectResponseFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->request, 'owl_admin_login')
            ->willReturn($redirectResponse);

        // Act
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertSame($redirectResponse, $response);
    }

    #[Test]
    public function it_uses_custom_redirect_route_when_specified_for_expired_token(): void
    {
        // Arrange
        $token = 'expired-token';
        $customRedirectRoute = 'custom_login_route';
        $redirectResponse = new Response();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->isInstanceOf(\DateInterval::class))
            ->willReturn(false);

        $session = $this->createMock(Session::class);
        $flashBag = $this->createMock(FlashBagInterface::class);
        
        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($session);

        $session
            ->expects($this->once())
            ->method('getBag')
            ->with('flashes')
            ->willReturn($flashBag);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_sylius', [])
            ->willReturn(['redirect' => $customRedirectRoute]);

        $this->request->attributes = $attributes;

        $this->redirectResponseFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->request, $customRedirectRoute)
            ->willReturn($redirectResponse);

        // Act
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertSame($redirectResponse, $response);
    }

    #[Test]
    public function it_creates_date_interval_with_correct_ttl(): void
    {
        // Arrange
        $token = 'valid-token';
        $customTtl = 'PT2H';
        
        $action = new RenderResetPasswordPageAction(
            $this->userRepository,
            $this->formFactory,
            $this->requestStack,
            $this->redirectResponseFactory,
            $this->twig,
            $customTtl
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->callback(function (\DateInterval $interval) use ($customTtl) {
                $expected = new \DateInterval($customTtl);
                return $interval->h === $expected->h && $interval->i === $expected->i;
            }))
            ->willReturn(true);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($this->createMock(FormView::class));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->willReturn('<html>content</html>');

        // Act
        $action($this->request, $token);

        // Assert - expectations verified by PHPUnit
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function tokenProvider(): array
    {
        return [
            'empty token' => ['token' => ''],
            'null token' => ['token' => null],
            'whitespace token' => ['token' => '   '],
            'special chars token' => ['token' => 'token!@#$%^&*()'],
            'long token' => ['token' => str_repeat('a', 100)],
            'uuid token' => ['token' => '550e8400-e29b-41d4-a716-446655440000'],
            'numeric token' => ['token' => '123456789'],
        ];
    }

    #[Test]
    #[DataProvider('tokenProvider')]
    public function it_handles_various_token_formats(?string $token): void
    {
        // Arrange
        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn(null);

        $this->redirectResponseFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->request, 'owl_admin_login')
            ->willReturn(new Response());

        // Act
        $response = ($this->action)($this->request, (string) $token);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
    }

    #[Test]
    public function it_handles_repository_exception(): void
    {
        // Arrange
        $token = 'valid-token';

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willThrowException(new \RuntimeException('Database error'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Database error');
        ($this->action)($this->request, $token);
    }

    #[Test]
    public function it_handles_form_factory_exception(): void
    {
        // Arrange
        $token = 'valid-token';

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
            ->willThrowException(new \RuntimeException('Form creation failed'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Form creation failed');
        ($this->action)($this->request, $token);
    }

    #[Test]
    public function it_handles_twig_render_exception(): void
    {
        // Arrange
        $token = 'valid-token';
        $formView = $this->createMock(FormView::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/security/reset_password.html.twig', [
                'form' => $formView,
            ])
            ->willThrowException(new \RuntimeException('Template rendering failed'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Template rendering failed');
        ($this->action)($this->request, $token);
    }

    #[Test]
    public function it_handles_invalid_ttl_format(): void
    {
        // Arrange
        $invalidTtl = 'invalid-ttl';
        
        $action = new RenderResetPasswordPageAction(
            $this->userRepository,
            $this->formFactory,
            $this->requestStack,
            $this->redirectResponseFactory,
            $this->twig,
            $invalidTtl
        );

        $token = 'valid-token';

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn($this->adminUser);

        // Act & Assert
        $this->expectException(\Exception::class);
        $action($this->request, $token);
    }

    #[Test]
    public function it_handles_admin_user_password_request_check_exception(): void
    {
        // Arrange
        $token = 'valid-token';

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => $token])
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->isInstanceOf(\DateInterval::class))
            ->willThrowException(new \RuntimeException('Password request check failed'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Password request check failed');
        ($this->action)($this->request, $token);
    }

    #[Test]
    public function it_always_uses_correct_template(): void
    {
        // Arrange
        $token = 'valid-token';
        $formView = $this->createMock(FormView::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo('@OwlAdmin/security/reset_password.html.twig'),
                $this->identicalTo(['form' => $formView])
            )
            ->willReturn('<html>content</html>');

        // Act
        ($this->action)($this->request, $token);

        // Assert - expectations verified by PHPUnit
    }
}
