<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    // Cette méthode permet de créer 9 produits fictifs
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; $i++) {
            $product = new Product();
            $product->setName("Produit " . $i);
            $product->setShortDescription("Description courte");
            // Faker est utilisé pour générer de fausses données (ici, 3 paragraphes)
            $product->setFullDescription(faker()->paragraphs(3, true));
            // Ici, Faker génère un nombre décimal avec 2 chiffres après la virgule, compris entre 4 et 50
            $product->setPrice(faker()->randomFloat(2, 4, 50));
            $product->setPicture("product" . $i . ".webp");
            $manager->persist($product);
        }

        $manager->flush();
    }
}
