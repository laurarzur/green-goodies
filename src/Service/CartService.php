<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;

final class CartService
{

    public static function getTotalOrderPrice(Order $order): float
    {
        $orderItems = $order->getOrderItems();

        $totalPrice = 0;

        foreach ($orderItems as $item) {
            $totalPrice += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $totalPrice;
    }

    public static function findOrderItemInCart(?Order $cart, Product $product): ?OrderItem
    {
        $orderItem = null;

        if ($cart) {
            foreach ($cart->getOrderItems() as $orderItemInCart) {
                if ($orderItemInCart->getProduct() === $product) {
                    $orderItem = $orderItemInCart;
                    break;
                }
            }
        }

        return $orderItem;
    }
}
