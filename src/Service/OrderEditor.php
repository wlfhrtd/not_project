<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\ProductRepository;

class OrderEditor
{
    public function populateForm (Order $order, $form)
    {
        $items = $order->getCart()->getItems();
        for ($i = 0; $i < count($items); $i++) {
            $form['cart']['items'][$i]['in_stock']->setData($items[$i]->getProduct()->getQuantityInStock());
            $form['cart']['items'][$i]['price']->setData($items[$i]->getProduct()->getPrice());
            $form['cart']['items'][$i]['item_total_price']->setData($items[$i]->getItemTotal()); // TODO let front calc this
        }
    }

    public function checkTotal(Order $order, $form): bool
    {
        $totalBack = $order->getCart()->getTotal();
        $totalFront = $form->get('total')->getData();

        if ($totalBack == $totalFront) {
            return true;
        }

        return false;
    }

    public function contains($originalItem, $items): bool
    {
        // TODO refactor: not only checks but does something as mutator
        $product = $originalItem->getProduct();
        foreach ($items as $item) {
            if ($product === $item->getProduct()) {
                $product->setQuantityInStock($product->getQuantityInStock() - ($item->getQuantity() - $originalItem->getQuantity()));
                return true;
            }
        }
        return false;
    }

    public function containsNewInOld($item, $originalItems): bool
    {
        $product = $item->getProduct();
        foreach ($originalItems as $originalItem) {
            if ($product === $originalItem->getProduct()) {
                return true;
            }
        }
        return false;
    }

    // TODO refactor this
    public function handleEdit(Order $order, $originalItems)
    {
        // extract products sold(in order_new) OR needed modification after order_edit
        $items = $order->getCart()->getItems();

        foreach ($originalItems as $originalItem) {
            if (!$this->contains($originalItem, $items)) {
                $product = $originalItem->getProduct();
                $product->setQuantityInStock($product->getQuantity() + $originalItem->getQuantity());
            }
        }

        foreach ($items as $item) {
            if (!$this->containsNewInOld($item, $originalItems)) {
                $product = $item->getProduct();
                $product->setQuantityInStock($product->getQuantityInStock() - $item->getQuantity());
            }
        }
    }

    public function handleNew(Order $order, ProductRepository $productRepository)
    {
        // extract products sold(in order_new) OR needed modification after order_edit
        $items = $order->getCart()->getItems();
        // order_new case
        // reduce product stock quantity by product item quantity sold in order
        foreach ($items as $item) {
            $itemProduct = $item->getProduct();
            $quantity_sold = $item->getQuantity();

            $stockProduct = $productRepository->findOneBy(['id' => $itemProduct->getId()]);
            $stockProduct->setQuantityInStock($stockProduct->getQuantityInStock() - $quantity_sold);
        }
    }
}
