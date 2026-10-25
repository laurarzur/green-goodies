<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; $i++) {
            $product = new Product();
            $product->setName("Produit " . $i);
            $product->setShortDescription("Description courte");
            $product->setFullDescription(faker()->paragraphs(3, true));
            $product->setPrice(faker()->randomFloat(2, 4, 50));
            $product->setPicture("product" . $i . ".webp");
            $manager->persist($product);
        }

        $manager->flush();
    }
}
