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

    /**
     * Cette méthode permet d'afficher la page d'accueil
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        $products = $this->repo->findAll();
        return $this->render('home.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * Cette méthode permet d'afficher le détail d'un produit 
     * @param ?Product $product
     * @param Request $request 
     * @param OrderRepository $orderRepo
     * @return Response
     */
    #[Route('product/{id}', name: 'app_product')]
    public function product(?Product $product, Request $request, OrderRepository $orderRepo): Response
    {
        if (!$product) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();

        // Le panier de l'utilisateur, s'il existe, est récupéré pour vérifier s'il contient déjà le produit
        $cart = $orderRepo->findOneBy(['customer' => $user, 'isConfirmed' => false]);
        $orderItem = CartService::findOrderItemInCart($cart, $product);

        // L'option 'in_cart' est utilisée dans le formulaire pour déterminer le label du bouton
        $form = $this->createForm(ProductType::class, $product, ['in_cart' => $orderItem !== null]);
        // Si le produit est déjà dans le panier, la quantité par défaut est celle déjà présente dans le panier
        $form->get('quantity')->setData($orderItem?->getQuantity() ?? 1);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le panier n'existe pas encore, il est créé
            if (!$cart) {
                $cart = new Order();
                $cart->setCustomer($user);
            }
            // Si le produit n'est pas déjà dans le panier, on créé l'élément du panier contenant le produit
            if (!$orderItem) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $cart->addOrderItem($orderItem);
            }
            // La quantité souhaitée est ajoutée ou modifiée
            $orderItem->setQuantity($form->get('quantity')->getData());
            // Une fois le produit ajouté au panier, le total est calculé
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
