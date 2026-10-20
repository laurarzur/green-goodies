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
    #[Route('/', name: 'app_home')]
    public function home(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        return $this->render('home.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('product/{id}', name: 'app_product')]
    public function product(?Product $product, Request $request, OrderRepository $orderRepo, EntityManagerInterface $em): Response
    {
        if (!$product) {
            return $this->redirectToRoute('app_products');
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

            $em->persist($cart);
            $em->persist($orderItem);
            $em->flush();

            $this->addFlash('success', 'Panier mis à jour');

            return $this->redirectToRoute('app_product', ['id' => $product->getId()]);
        }

        return $this->render('product/details.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }
}
