<?php

namespace App\Controller\ApiController;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function getProducts(ProductRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $products = $repo->findAll();
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product']);
        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }
}
