<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;

final class CartService
{

    /**
     * Cette méthode permet de calculer le prix total d'une commande ou d'un panier
     * @param Order $order
     * @return float
     */
    public static function getTotalOrderPrice(Order $order): float
    {
        $orderItems = $order->getOrderItems();

        $totalPrice = 0;

        // Pour chaque élément dans la commande, le prix du produit est multiplié par la quantité voulue
        foreach ($orderItems as $item) {
            $totalPrice += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $totalPrice;
    }

    /**
     * Cette méthode permet de retrouver un élément (produit + quantité) dans le panier
     * @param ?Order $cart 
     * @param Product $product
     * @return ?OrderItem
     */
    public static function findOrderItemInCart(?Order $cart, Product $product): ?OrderItem
    {
        $orderItem = null;
        // Il est possible que la variable $cart contienne null
        if ($cart) {
            foreach ($cart->getOrderItems() as $orderItemInCart) {
                // Si un élément du panier contient le produit, il est récupéré dans la variable $orderItem
                if ($orderItemInCart->getProduct() === $product) {
                    $orderItem = $orderItemInCart;
                    break;
                }
            }
        }

        return $orderItem;
    }
}
