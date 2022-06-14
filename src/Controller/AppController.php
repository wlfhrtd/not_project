<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        //TODO add $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); for admin routes or Not cuz of acceesss control
        // #[IsGranted('ROLE_ADMIN')]  #[IsGranted('ROLE_SUPER_ADMIN')] {% if is_granted('ROLE_ADMIN') %}

        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
    }
}
