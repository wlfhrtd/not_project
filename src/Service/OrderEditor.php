<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Order;
use Symfony\Component\Form\FormInterface;

class OrderEditor
{
    private $originalItems; // contains order.cart.items 'backup' to handle order edit action
    private $errorMessages; // if returns null handle_ methods return true

    const ERROR_ORDER_NOT_BLANK = 'NotBlank';
    const ERROR_CART_ITEM_POSITIVE = 'Positive';
    const ERROR_CART_ITEM_POSITIVE_OR_ZERO = 'PositiveOrZero';
    const ERROR_CART_ITEM_UNIQUE = 'Unique';
    const ERROR_ORDER_TOTAL_IDENTICAL_TO = 'IdenticalTo';

    public function setError(CartItem|Order|string $obj, string $error): void
    {
        $this->errorMessages[] = $error
            . ' violation @ '
            . $obj
        ;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    public function clearErrorMessages()
    {
        return $this->errorMessages = null;
    }

    /**
     * populates several form values to reduce ajax requests number
     * also helps with order action edit
     */
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

    public function contains(CartItem $needle, $items)
    {
        foreach ($items as $item) {
            if ($needle->equals($item)) {
                return $item;
            }
        }
        return false;
    }

    private function handleEdit($items): bool
    {
        // traverse original items
        foreach ($this->originalItems as $originalItem) {
            if (!$newItem = $this->contains($originalItem, $items)) {
                // return products if !contains
                if(!$originalItem->getProduct()->setQuantityInStock($originalItem->getProduct()->getQuantityInStock() + $originalItem->getQuantity())) {
                    $this->setError($originalItem, self::ERROR_CART_ITEM_POSITIVE_OR_ZERO);
                }
            } else {
                // calc&apply changes
                if(!$originalItem->getProduct()->setQuantityInStock($originalItem->getProduct()->getQuantityInStock() - ($newItem->getQuantity() - $originalItem->getQuantity()))) {
                    $this->setError($newItem, self::ERROR_CART_ITEM_POSITIVE_OR_ZERO);
                }
            }
        }

        // traverse new items
        foreach ($items as $item) {
            if (!$this->contains($item, $this->originalItems)) {
                if(!$item->getProduct()->setQuantityInStock($item->getProduct()->getQuantityInStock() - $item->getQuantity())) {
                    $this->setError($item, self::ERROR_CART_ITEM_POSITIVE_OR_ZERO);
                }
            }
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }

    // TODO deduplication items by product criteria in 1) js 2) backend
    // TODO add validation in entities (setters, like for product.setQUantityinstock)
    // TODO refactor js, figure out whats with indexes, test more intensive (order cart items >3, buggy)
    // TODO move stuff from forms to templates
    // TODO admin, security
    // TODO learn locks, shared resources, optimistic, pessimistic etc
    // TODO homepage
    // TODO try to get rid of CartType - EntityType form?


    // TODO similar js deduplication to prevent submit
    /**
     * cartItem duplication by product is not allowed and silently fails
     * cart->addItem() return false and does nothing if duplication occurs
     * so duplication happens only in form but not in order.cart entity
     * that's why have to mess with form directly
     */
    public function checkFormDuplication(FormInterface $form): bool
    {
        $itemsForm = $form->get('cart')->get('items'); // possible nulls if index is wrong (e.g. 0, 4 and missing 1,2,3 so foreach will fail)
        $items = [];
        foreach ($itemsForm as $itemForm) {
            if ($itemForm) {
                $items[] = $itemForm->getData();
            }
        }
        for ($i = 0; $i < count($items); $i++) {
            for ($j = $i + 1; $j < count($items); $j++) {
                if ($items[$i]->equals($items[$j])) {
                    $this->setError($items[$j], self::ERROR_CART_ITEM_UNIQUE);
                }
            }
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }

    public function handle(Order $order, FormInterface $form): bool
    {
        if (!$this->checkFormDuplication($form)) {
            return false;
        }

        $items = $order->getCart()->getItems();

        if ($order->getId()) {
            // action edit
            if (!$this->handleEdit($items)) {
                return false;
            }
        }

        // action new
        if (!$order->getId()) {
            // action new
            if (!$this->handleNew($items)) {
                return false;
            }
        }

        if (!$this->checkTotal($order, $form)) {
            return false;
        }

        return true;
    }

    /**
     * order_new
     *
     * returns false if violated PositiveOrZero constraint of product.quantityInStock field
     * make response in controller for this
     */
    private function handleNew($items): bool
    {
        // reduce product stock quantity by product item quantity sold in order
        foreach ($items as $item) {
            if (!$item->getProduct()->setQuantityInStock($item->getProduct()->getQuantityInStock() - $item->getQuantity())) {
                $this->setError($item, self::ERROR_CART_ITEM_POSITIVE_OR_ZERO);
            }
        }

        if (!$this->errorMessages) {
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
            if (!$item->getProduct()->setQuantityInStock($item->getProduct()->getQuantityInStock() + $item->getQuantity())) {
                $this->setError($item, self::ERROR_CART_ITEM_POSITIVE_OR_ZERO);
            }
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }

    public function checkTotal(Order $order, FormInterface $form): bool
    {
        $totalBack = $order->getCart()->getTotal();
        $totalFront = $form->get('total')->getData();

        if ($totalBack != $totalFront) {
            $errorMessage = 'Form total price: ' . $totalFront . '; Backend total price: ' . $totalBack;
            $this->setError($errorMessage, self::ERROR_ORDER_TOTAL_IDENTICAL_TO);
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }

    /**
     * order finish action is disallowed if order.cart is empty
     * or if any cart.cartItem.quantity is <= 0
     *
     * only order with order.status==in_progress should be here
     * accordingly to restrictions in View
     */
    public function finish(Order $order): bool
    {
        $items = $order->getCart()->getItems();

        if ($items->isEmpty()) {
            $this->setError($order, self::ERROR_ORDER_NOT_BLANK);
        }

        foreach ($items as $item) {
            if ($item->getQuantity() <= 0) {
                $this->setError($item, self::ERROR_CART_ITEM_POSITIVE);
            }
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }
}
