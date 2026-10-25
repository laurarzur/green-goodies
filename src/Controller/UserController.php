<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/user', name: 'app_user')]
    public function index(OrderRepository $orderRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_home');
        }

        $orders = $orderRepo->findBy(['customer' => $this->getUser(), 'isConfirmed' => true]);

        return $this->render('user/index.html.twig', [
            'orders' => $orders
        ]);
    }

    private function setApiAccess(bool $value): Response
    {
        $user = $this->getUser();

        /** @var User $user */
        if ($user) {
            $user->setHasApiAccess($value);
            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_user');
    }

    #[Route('/user/api/enable', name: 'app_user_api_enable')]
    public function enableApiAccess(): Response
    {
        return $this->setApiAccess(true);
    }

    #[Route('/user/api/disable', name: 'app_user_api_disable')]
    public function disableApiAccess(): Response
    {
        return $this->setApiAccess(false);
    }

    #[Route('/user/delete', name: 'app_user_delete')]
    public function deleteUser(Security $security): Response
    {
        $user = $this->getUser();

        if ($user) {
            $security->logout(false);
            $this->em->remove($user);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}
