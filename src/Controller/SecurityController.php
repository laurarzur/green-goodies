<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Cette méthode permet de connecter un utilisateur 
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // S'il y a des erreurs, elles sont récupérées ici
        $error = $authenticationUtils->getLastAuthenticationError();

        // Le dernier username (ici, adresse email) de l'utilisateur est aussi récupéré
        $lastUsername = $authenticationUtils->getLastUsername();

        // Ces 2 éléments sont passés à la vue si le formulaire doit être réaffiché 
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Cette méthode est interceptée par le firewall main
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
