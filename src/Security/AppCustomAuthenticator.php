<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private Security $security;
    private HttpClientInterface $httpClient;

    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security, HttpClientInterface $httpClient)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->httpClient = $httpClient;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token');

        $request->getSession()->start(); // Ensure the session is started
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if (!$this->isCaptchaValid($request)) {
            throw new CustomUserMessageAuthenticationException('Invalid CAPTCHA.');
        }

        $user = $this->security->getUser();
        $roles = $user ? $user->getRoles() : [];

        // Debug roles if needed
        // dump($roles); exit;

        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_base'));
        } 
        elseif (in_array('ROLE_AGRI', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_front'));
        }
        
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function isCaptchaValid(Request $request): bool
    {
        $hCaptchaResponse = $request->request->get('h-captcha-response');
        if (!$hCaptchaResponse) {
            return false;
        }

        $secret = $_ENV['HCAPTCHA_SECRET_KEY'];
        $response = $this->httpClient->request('POST', 'https://hcaptcha.com/siteverify', [
            'body' => [
                'secret' => $secret,
                'response' => $hCaptchaResponse,
                'remoteip' => $request->getClientIp(),
            ],
        ]);

        $result = $response->toArray();

        // Debugging CAPTCHA response if needed
        // dump($result); exit;

        return $result['success'] ?? false;
    }
}
