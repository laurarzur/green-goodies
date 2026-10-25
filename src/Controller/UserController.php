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

    /**
     * Cette méthode permet d'afficher le compte de l'utilisateur
     * @param OrderRepository $orderRepo
     * @return Response
     */
    #[Route('/user', name: 'app_user')]
    public function index(OrderRepository $orderRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_home');
        }

        // Toutes les commandes passées par l'utilisateur (donc hors panier en cours) sont affichées sur la page
        $orders = $orderRepo->findBy(['customer' => $this->getUser(), 'isConfirmed' => true]);

        return $this->render('user/index.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * Cette méthode permet de modifier l'accès API de l'utilisateur
     * @param bool $value
     * @return Response
     */
    private function setApiAccess(bool $value): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user) {
            $user->setHasApiAccess($value);
            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_user');
    }

    /**
     * Cette méthode permet d'activer l'accès API de l'utilisateur
     * @return Response
     */
    #[Route('/user/api/enable', name: 'app_user_api_enable')]
    public function enableApiAccess(): Response
    {
        return $this->setApiAccess(true);
    }

    /**
     * Cette méthode permet de désactiver l'accès API de l'utilisateur
     * @return Response
     */
    #[Route('/user/api/disable', name: 'app_user_api_disable')]
    public function disableApiAccess(): Response
    {
        return $this->setApiAccess(false);
    }

    /**
     * Cette méthode permet de supprimer le compte de l'utilisateur
     * @param Security $security
     * @return Response
     */
    #[Route('/user/delete', name: 'app_user_delete')]
    public function deleteUser(Security $security): Response
    {
        $user = $this->getUser();

        if ($user) {
            // L'utilisateur est déconnecté pour éviter toute erreur
            $security->logout(false);
            $this->em->remove($user);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}
