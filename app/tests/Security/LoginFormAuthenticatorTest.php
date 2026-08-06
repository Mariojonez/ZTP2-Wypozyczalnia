<?php

/**
 * LoginFormAuthenticator test.
 */

namespace App\Tests\Security;

use App\Security\LoginFormAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

/**
 * Class LoginFormAuthenticatorTest.
 */
class LoginFormAuthenticatorTest extends TestCase
{
    private UrlGeneratorInterface $urlGenerator;
    private LoginFormAuthenticator $authenticator;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->urlGenerator = $this->createStub(UrlGeneratorInterface::class);

        $this->authenticator = new LoginFormAuthenticator(
            $this->urlGenerator
        );
    }

    /**
     * Test supports method returns true for login POST request.
     */
    public function testSupportsReturnsTrueForLoginPostRequest(): void
    {
        $request = new Request();
        $request->attributes->set('_route', 'app_login');
        $request->setMethod('POST');

        $result = $this->authenticator->supports($request);

        $this->assertTrue($result);
    }

    /**
     * Test supports method returns false for non-login route.
     */
    public function testSupportsReturnsFalseForDifferentRoute(): void
    {
        $request = new Request();
        $request->attributes->set('_route', 'different_route');
        $request->setMethod('POST');

        $result = $this->authenticator->supports($request);

        $this->assertFalse($result);
    }

    /**
     * Test supports method returns false for non-POST request.
     */
    public function testSupportsReturnsFalseForNonPostRequest(): void
    {
        $request = new Request();
        $request->attributes->set('_route', 'app_login');
        $request->setMethod('GET');

        $result = $this->authenticator->supports($request);

        $this->assertFalse($result);
    }

    /**
     * Test authenticate method.
     */
    public function testAuthenticateCreatesPassportAndStoresLastUsername(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session
            ->expects($this->once())
            ->method('set')
            ->with(
                SecurityRequestAttributes::LAST_USERNAME,
                'test@example.com'
            );

        $request = new Request(
            [],
            [
                'email' => 'test@example.com',
                'password' => 'password123',
                '_csrf_token' => 'csrf-token',
            ]
        );

        $request->setSession($session);

        $passport = $this->authenticator->authenticate($request);

        $this->assertInstanceOf(
            UserBadge::class,
            $passport->getBadge(UserBadge::class)
        );

        $this->assertSame(
            'test@example.com',
            $passport->getBadge(UserBadge::class)->getUserIdentifier()
        );

        $this->assertTrue(
            $passport->hasBadge(CsrfTokenBadge::class)
        );
    }

    /**
     * Test onAuthenticationSuccess redirects to target path.
     */
    public function testOnAuthenticationSuccessRedirectsToTargetPath(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session
            ->expects($this->once())
            ->method('get')
            ->with('_security.main.target_path')
            ->willReturn('/target');

        $request = new Request();
        $request->setSession($session);

        $token = $this->createStub(TokenInterface::class);

        $response = $this->authenticator->onAuthenticationSuccess(
            $request,
            $token,
            'main'
        );

        $this->assertInstanceOf(
            RedirectResponse::class,
            $response
        );

        $this->assertSame(
            '/target',
            $response->getTargetUrl()
        );
    }

    /**
     * Test onAuthenticationSuccess redirects to default route.
     */
    public function testOnAuthenticationSuccessRedirectsToDefaultRoute(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('book_index')
            ->willReturn('/books');

        $authenticator = new LoginFormAuthenticator($urlGenerator);

        $session = $this->createMock(SessionInterface::class);

        $session
            ->expects($this->once())
            ->method('get')
            ->with('_security.main.target_path')
            ->willReturn(null);

        $request = new Request();
        $request->setSession($session);

        $token = $this->createStub(TokenInterface::class);

        $response = $authenticator->onAuthenticationSuccess(
            $request,
            $token,
            'main'
        );

        $this->assertInstanceOf(
            RedirectResponse::class,
            $response
        );

        $this->assertSame(
            '/books',
            $response->getTargetUrl()
        );
    }

    /**
     * Test getLoginUrl().
     */
    public function testGetLoginUrl(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('app_login')
            ->willReturn('/login');

        $reflection = new \ReflectionClass(LoginFormAuthenticator::class);

        /** @var LoginFormAuthenticator $authenticator */
        $authenticator = $reflection->newInstance($urlGenerator);

        $method = $reflection->getMethod('getLoginUrl');
        $method->setAccessible(true);

        $request = new Request();

        $result = $method->invoke($authenticator, $request);

        $this->assertSame('/login', $result);
    }
}
