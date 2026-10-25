<?php

namespace App\Controller\ApiController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;


final class ApiSecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login')]
    public function login()
    {
        throw new \LogicException("This method can be blank - it will be intercepted by the firewall.");
    }
}
