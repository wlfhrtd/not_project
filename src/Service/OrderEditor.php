<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Order;
use Symfony\Component\Form\FormInterface;

class OrderEditor
{
    private $originalItems; // contains order.cart.items 'backup' to handle order edit action
    private $errorMessages; // if returns null handle_ methods return true
    
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    public function clearErrorMessages()
    {
        return $this->errorMessages = null;
    }
    
    public function populateForm (Order $order, $form): void
    {
        /*
         * new order is not persisted yet and has no id => don't need backup for action new
         * on action edit order has id => need backup
         *
         * 2nd check: do not make backup again if already done
         */
        if ($order->getId() && !$this->originalItems) {
            $this->backupOriginalItems($order);
        }

        // to reduce number of ajax requests
        $items = $order->getCart()->getItems();
        for ($i = 0; $i < count($items); $i++) {
            $form['cart']['items'][$i]['in_stock']->setData($items[$i]->getProduct()->getQuantityInStock());
            $form['cart']['items'][$i]['price']->setData($items[$i]->getProduct()->getPrice());
        }
    }

    /**
     * backups items from loaded order to handle edit action later by comparison
     */
    private function backupOriginalItems(Order $order): void
    {
        $this->originalItems = [];

        foreach ($order->getCart()->getItems() as $item) {
            /*
             * new() for 'really' original items
             * can get just only pointers from $order directly that will lead to changes of original items on order edit
             * (they will be the same as in modified order)
             */
            $this->originalItems[] = (new CartItem())
                ->setProduct($item->getProduct())
                ->setQuantity($item->getQuantity());
        }
    }

    public function setError(CartItem $cartItem): void
    {
        $this->errorMessages['items'][] = 'PositiveOrZero violation @ '
            . $cartItem->getProduct()->getName()
            . '; quantity: ' . $cartItem->getQuantity()
            . '; quantity in stock: '
            . $cartItem->getProduct()->getQuantityInStock()
        ;
    }

    // TODO REFACTOR ALL I GUESS IT'S GONNA BE X1 2-DIMENSIONAL =I =J TRAVERSE AND X1 =I REGULAR
    public function contains($originalItem, $items): bool
    {
        // TODO refactor: not only checks but does something as mutator
        $product = $originalItem->getProduct();
        foreach ($items as $item) {
            if ($product === $item->getProduct()) {
                if(!$product->setQuantityInStock($product->getQuantityInStock() - ($item->getQuantity() - $originalItem->getQuantity()))) {
                    $this->setError($item);
                }
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
    public function handleEdit(Order $order, FormInterface $form): bool
    {
        $this->checkTotal($order, $form);

        // extract products sold(in order_new) OR needed modification after order_edit
        // we have modified order and items here
        $items = $order->getCart()->getItems();

        foreach ($this->originalItems as $originalItem) {
            if (!$this->contains($originalItem, $items)) {
                $product = $originalItem->getProduct();
                if(!$product->setQuantityInStock($product->getQuantityInStock() + $originalItem->getQuantity())) {
                    $this->setError($originalItem);
                }
            }
        }

        foreach ($items as $item) {
            if (!$this->containsNewInOld($item, $this->originalItems)) {
                $product = $item->getProduct();
                if(!$product->setQuantityInStock($product->getQuantityInStock() - $item->getQuantity())) {
                    $this->setError($item);
                }
            }
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }

    public function checkTotal(Order $order, FormInterface $form): void
    {
        $totalBack = $order->getCart()->getTotal();
        $totalFront = $form->get('total')->getData();

        if ($totalBack != $totalFront) {
            $this->errorMessages['total'] = 'Order total price mismatch! Front: ' . $totalFront . ' Back: ' . $totalBack;
        }
    }

    /**
     * order_new
     *
     * returns false if violated non-negative constraint of product.quantityInStock field
     * make response in controller for this
     */
    public function handleNew(Order $order, FormInterface $form): bool
    {
        $this->checkTotal($order, $form);

        // extract products sold(in order_new)
        $items = $order->getCart()->getItems();
        // reduce product stock quantity by product item quantity sold in order
        foreach ($items as $item) {
            if (!$this->sell($item)) {
                $this->setError($item);
            }
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }

    /**
     * $product->setQuantityInStock(int n) returns false if n is negative and returns true if success
     *
     * sell($cartItem) returns false if setQuantityInStock(n) returned false
     * that means we violated entity logic and failed to update product.quantityInStock field
     * that is good time for throw or response
     */
    public function sell(CartItem $cartItem): bool
    {
        $itemProduct = $cartItem->getProduct();
        $quantitySold = $cartItem->getQuantity();

        if ($itemProduct->setQuantityInStock($itemProduct->getQuantityInStock() - $quantitySold)) {
            return true;
        }

        return false;
    }

    /**
     * PositiveOrZero quantityInStock violation can happen if cartItem.quantity is big negative for some reason
     * if happens - redirect to order edit view in controller
     */
    public function cancel(Order $order): bool
    {
        $items = $order->getCart()->getItems();
        foreach ($items as $item) {
            $quantity = $item->getQuantity();
            $product = $item->getProduct();
            if (!$product->setQuantityInStock($product->getQuantityInStock() + $quantity)) {
                $this->setError($item);
            }
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }
}
