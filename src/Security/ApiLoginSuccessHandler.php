<?php

namespace App\Security;

use App\Entity\User;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class ApiLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        #[Autowire(service: 'lexik_jwt_authentication.handler.authentication_success')]
        private AuthenticationSuccessHandlerInterface $inner
    ) {}

    #[Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user->hasApiAccess()) {
            return new JsonResponse(['status' => 403, 'message' => "Votre accès API n'est pas activé"], Response::HTTP_FORBIDDEN);
        }

        return $this->inner->onAuthenticationSuccess($request, $token);
    }
}
