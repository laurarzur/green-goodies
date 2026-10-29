<?php

namespace App\Controller\ApiController;

use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiProductController extends AbstractController
{
    /**
     * Cette méthode permet d'afficher les produits pour un utilisateur de l'API
     * @param ProductRepository $repo
     * @param SerializerInterface $serializer
     * @param UrlHelper $urlHelper
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function getProducts(ProductRepository $repo, SerializerInterface $serializer, UrlHelper $urlHelper): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['status' => 401, 'message' => 'Connexion requise'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->hasApiAccess()) {
            return new JsonResponse(['status' => 403, 'message' => "Votre accès API n'est pas activé"], Response::HTTP_FORBIDDEN);
        }

        $products = $repo->findAll();

        /* Pour chaque produit, le chemin de l'image est transformé en URL 
        Ex : "https://127.0.0.1:8000/img/products/product1.webp" au lieu de "product1.webp" */
        foreach ($products as $product) {
            $product->setPicture($urlHelper->getAbsoluteUrl('/img/products/' . $product->getPicture()));
        }

        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product']);
        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }
}
