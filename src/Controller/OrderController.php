<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $repo,
        private EntityManagerInterface $em
    ) {}

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $cart = $this->repo->findOneBy(['customer' => $this->getUser(), 'isConfirmed' => false]);

        return $this->render('order/cart.html.twig', [
            'cart' => $cart
        ]);
    }

    #[Route('/cart/delete', name: 'app_cart_delete')]
    public function deleteCart(): Response
    {
        $cart = $this->repo->findOneBy(['customer' => $this->getUser(), 'isConfirmed' => false]);

        if ($cart) {
            $this->em->remove($cart);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/confirm', name: 'app_order_confirm')]
    public function confirmOrder(): Response
    {
        $cart = $this->repo->findOneBy(['customer' => $this->getUser(), 'isConfirmed' => false]);

        if ($cart) {
            $cart->setIsConfirmed(true);
            $cart->setCreatedAt(new DateTimeImmutable());
            $this->em->persist($cart);
            $this->em->flush();
            $this->addFlash('success', 'Commande validée');
        }

        return $this->redirectToRoute('app_cart');
    }
}
