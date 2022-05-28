<?php

namespace App\EntityListener;

use App\Entity\Product;

class ProductEntityListener
{
    public function prePersist(Product $product): void
    {
        // default: product.status==new_product, product.quantityInStock = 0; no actions required

        // added some units in stock when created new product
        if ($product->getQuantityInStock() !== 0 && $product->getStatus() === Product::STATUS_PRODUCT_NEW) {
            $product->setStatus(Product::STATUS_PRODUCT_IN_STOCK);
        }
    }

    // event triggered when changeset (~edit action of existing entity) of entity is NOT empty, not triggered otherwise
    // all changes to entity should be already visible (except changes to collections and associations accordingly to docs)
    public function preUpdate(Product $product): void
    {
        // ignore status_hidden (removed product)
        if ($product->getStatus() !== Product::STATUS_PRODUCT_HIDDEN) {
            // ~sold out
            if ($product->getQuantityInStock() == 0) {
                $product->setStatus(Product::STATUS_PRODUCT_OUT_OF_STOCK);
            }
            // ~invoice && partial reduce/addition
            // status_new, status_out_of_stock affected
            if ($product->getQuantityInStock() != 0) {
                $product->setStatus(Product::STATUS_PRODUCT_IN_STOCK);
            }
        }
    }
}
