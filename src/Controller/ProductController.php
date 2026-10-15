<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function product(?Product $product): Response
    {
        if (!$product) {
            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/details.html.twig', [
            'product' => $product
        ]);
    }
}
