<?php
namespace App\Controller;

use App\Security\UserConfirmationService;
use Symfony\Component\HttpFoundation\{RedirectResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/")
*/
class WebController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
    */
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/confirm-user/{token}", name="default_confirm_token")
     * @param string $token
     * @param UserConfirmationService $userConfirmationService
     * @return RedirectResponse
     * @throws \App\Exception\InvalidConfirmationTokenException
     */
    public function confirmUser(string $token, UserConfirmationService $userConfirmationService): RedirectResponse
    {
        $userConfirmationService->confirmUser($token);

        return $this->redirectToRoute('default_index');
    }
}
