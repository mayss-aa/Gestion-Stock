<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Get last username; force it to be a string.
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($lastUsername === null) {
            $lastUsername = '';
        }

        // Get error if authentication fails
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Handle form submission
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $captchaResponse = $request->request->get('captcha'); // assuming 'captcha' is the name of the captcha input field

            // Validate inputs (simple validation)
            if (empty($email) || empty($password)) {
                
                return $this->redirectToRoute('app_login'); // Redirect to login if email or password is missing
            }

            // Verify the CAPTCHA (this should still work, but can be moved to the authenticator too)
            if (!$this->verifyHCaptcha($captchaResponse, $request)) {
                
                return $this->redirectToRoute('app_login'); // Redirect to login if CAPTCHA fails
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error, // Show Symfony's default error message if any
            'hCaptchaSiteKey' => $_ENV['HCAPTCHA_SITE_KEY'],
        ]);
    }

    private function verifyHCaptcha(?string $response, Request $request): bool
    {
        if (empty($response)) {
            return false;
        }

        $secret = $_ENV['HCAPTCHA_SECRET_KEY'];
        $url = 'https://hcaptcha.com/siteverify';

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'secret' => $secret,
                'response' => $response,
                'remoteip' => $request->getClientIp(),
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            return $data['success'] ?? false;
        } catch (\Exception $e) {
            return false; // Fail-safe: Don't allow login if request fails
        }
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
