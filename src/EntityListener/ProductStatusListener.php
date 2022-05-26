<?php

namespace App\EntityListener;

use App\Entity\Product;

class ProductStatusListener
{
    public function postLoad(Product $product): void
    {
        if ($product->getQuantityInStock() === 0 && $product->getStatus() === Product::STATUS_PRODUCT_IN_STOCK) {

            $product->setStatus(Product::STATUS_PRODUCT_OUT_OF_STOCK);
        }

        if ($product->getQuantityInStock() !== 0 && $product->getStatus() === Product::STATUS_PRODUCT_OUT_OF_STOCK) {

            $product->setStatus(Product::STATUS_PRODUCT_IN_STOCK);
        }
    }
}
