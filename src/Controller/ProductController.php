<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $repo,
        private EntityManagerInterface $em
    ) {}

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        $products = $this->repo->findAll();
        return $this->render('home.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('product/{id}', name: 'app_product')]
    public function product(?Product $product, Request $request, OrderRepository $orderRepo): Response
    {
        if (!$product) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();

        $cart = $orderRepo->findOneBy(['customer' => $user, 'isConfirmed' => false]);

        $orderItem = CartService::findOrderItemInCart($cart, $product);

        $form = $this->createForm(ProductType::class, $product, ['in_cart' => $orderItem !== null]);
        $form->get('quantity')->setData($orderItem?->getQuantity() ?? 1);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$cart) {
                $cart = new Order();
                $cart->setCustomer($user);
            }

            if (!$orderItem) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $cart->addOrderItem($orderItem);
            }

            $orderItem->setQuantity($form->get('quantity')->getData());
            $cart->setTotalPrice(CartService::getTotalOrderPrice($cart));

            $this->em->persist($cart);
            $this->em->persist($orderItem);
            $this->em->flush();

            return $this->redirectToRoute('app_cart');
        }

        return $this->render('product/details.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }
}
