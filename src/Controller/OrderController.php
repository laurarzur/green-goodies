<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(OrderRepository $repo): Response
    {
        $cart = $repo->findOneBy(['customer' => $this->getUser(), 'isConfirmed' => false]);

        return $this->render('order/cart.html.twig', [
            'cart' => $cart
        ]);
    }

    #[Route('/cart/delete', name: 'app_cart_delete')]
    public function deleteCart(OrderRepository $repo, EntityManagerInterface $em): Response
    {
        $cart = $repo->findOneBy(['customer' => $this->getUser(), 'isConfirmed' => false]);

        if ($cart) {
            $em->remove($cart);
            $em->flush();
        }

        return $this->redirectToRoute('app_cart');
    }
}
