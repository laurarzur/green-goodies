<?php

namespace App\Controller\ApiController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;


final class ApiSecurityController extends AbstractController
{
    // Cette méthode est interceptée par le firewall login
    #[Route('/api/login', name: 'api_login')]
    public function login(): never
    {
        throw new \LogicException("This method can be blank - it will be intercepted by the firewall.");
    }
}
