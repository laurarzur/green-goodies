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

    private function setApiAccess(bool $value, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        /** @var User $user */
        if ($user) {
            $user->setHasApiAccess($value);
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_user');
    }

    #[Route('/user/api/enable', name: 'app_user_api_enable')]
    public function enableApiAccess(EntityManagerInterface $em): Response
    {
        return $this->setApiAccess(true, $em);
    }

    #[Route('/user/api/disable', name: 'app_user_api_disable')]
    public function disableApiAccess(EntityManagerInterface $em): Response
    {
        return $this->setApiAccess(false, $em);
    }

    #[Route('/user/delete', name: 'app_user_delete')]
    public function deleteUser(EntityManagerInterface $em, Security $security): Response
    {
        $user = $this->getUser();

        if ($user) {
            $security->logout(false);
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}
