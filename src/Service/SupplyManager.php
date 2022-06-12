<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Order;

class SupplyManager
{
    private $originalItems; // contains order.cart.items 'backup' to handle order edit action
    private $errorMessages; // if returns false handle_ methods return true

    const ORDER_ACTION_NEW = 'OrderActionNew';
    const ORDER_ACTION_CANCEL = 'OrderActionCancel';
    const ORDER_ACTION_EDIT = 'OrderActionEdit';
    const ORDER_ACTION_FINISH = 'OrderActionFinish';

    const ERROR_ORDER_NOT_BLANK = 'NotBlank';
    const ERROR_CART_ITEM_POSITIVE = 'Positive';
    const ERROR_CART_ITEM_POSITIVE_OR_ZERO = 'PositiveOrZero';

    private function setError(CartItem|Order|string $obj, string $error): void
    {
        if ($obj instanceof CartItem || $obj instanceof Order) {
            $this->errorMessages[] = $error
                . ' violation @ '
                . $obj->toLongString()
            ;
            return;
        }

        $this->errorMessages[] = $error
            . ' violation @ '
            . $obj
        ;
    }

    public function getErrorMessages(): ?array
    {
        return $this->errorMessages;
    }

    public function clearErrorMessages(): void
    {
        $this->errorMessages = null;
    }

    /**
     * backups loaded order cart items to handle edit action later by comparison
     */
    public function backupOriginalItems(Order $order): void
    {
        if (!$this->originalItems) {

            $this->originalItems = [];

            foreach ($order->getCart()->getItems() as $item) {
                // new() for 'really' original items NOT just references
                $this->originalItems[] = (new CartItem())
                    ->setProduct($item->getProduct())
                    ->setQuantity($item->getQuantity());
            }
        }
    }

    private function contains(CartItem $needle, $items)
    {
        foreach ($items as $item) {
            if ($needle->equals($item)) {
                return $item;
            }
        }
        return false;
    }

    private function merge($itemsModified, $itemsOrigMatch): void
    {
        $products = [];
        $modifiedQuantities = [];
        foreach ($itemsModified as $item) {
            $products[] = $item->getProduct();
            $modifiedQuantities[] = $item->getQuantity();
        }
        $oldQuantities = [];
        foreach ($itemsOrigMatch as $item) {
            $oldQuantities[] = $item->getQuantity();
        }
        $oldValues = [];
        foreach ($products as $product) {
            $oldValues[] = $product->getQuantityInStock();
        }
        $diff = [];
        foreach ($modifiedQuantities as $k => $modifiedQuantity) {
            $diff[] = $modifiedQuantity - $oldQuantities[$k];
        }
        $newValues = [];
        foreach ($oldValues as $k => $oldValue) {
            $newValues[] = $oldValue - $diff[$k];
        }
        $this->validateAndApply($newValues, $itemsModified, $products);
    }

    private function handleEdit($items): void
    {
        $itemsReturned = [];
        $itemsModified = [];
        $itemOrigMatch = [];
        $itemsSold = [];

        foreach ($this->originalItems as $originalItem) {
            if (!$newItem = $this->contains($originalItem, $items)) {
                $itemsReturned[] = $originalItem;
            } else {
                $itemsModified[] = $newItem;
                $itemOrigMatch[] = $originalItem;
            }
        }

        foreach ($items as $item) {
            if (!$this->contains($item, $this->originalItems)) {
                $itemsSold[] = $item;
            }
        }

        $this->reduce($itemsSold);
        $this->increase($itemsReturned);
        $this->merge($itemsModified, $itemOrigMatch);
    }

    // TODO admin, security
    // TODO learn locks, shared resources, optimistic, pessimistic etc
    // TODO homepage

    public function manage(Order $order, string $action): bool
    {
        try {
            match ($action) {
                self::ORDER_ACTION_NEW => self::reduce($order->getCart()->getItems()),
                self::ORDER_ACTION_CANCEL => self::increase($order->getCart()->getItems()),
                self::ORDER_ACTION_EDIT => self::handleEdit($order->getCart()->getItems()),
                self::ORDER_ACTION_FINISH => self::finish($order),
            };
        } catch (\UnhandledMatchError $e) {
            dd($e);
        }

        if (!$this->errorMessages) {
            return true;
        }

        return false;
    }

    private function validateAndApply($newValues, $items, $products): void
    {
        foreach ($newValues as $k => $newValue) {
            if ($newValue < 0) {
                $this->setError($items[$k], self::ERROR_CART_ITEM_POSITIVE_OR_ZERO);
            }
        }
        if (!$this->errorMessages) {
            foreach ($products as $k => $product) {
                $product->setQuantityInStock($newValues[$k]);
            }
        }
    }

    private function reduce($items): void
    {
        $products = [];
        $amountSold = [];
        foreach ($items as $item) {
            $products[] = $item->getProduct();
            $amountSold[] = $item->getQuantity();
        }
        $oldValues = [];
        foreach ($products as $product) {
            $oldValues[] = $product->getQuantityInStock();
        }
        $newValues = [];
        foreach ($oldValues as $k => $oldValue) {
            $newValues[$k] = $oldValue - $amountSold[$k];
        }
        $this->validateAndApply($newValues, $items, $products);
    }

    private function increase($items): void
    {
        $products = [];
        $amountReturned = [];
        foreach ($items as $item) {
            $products[] = $item->getProduct();
            $amountReturned[] = $item->getQuantity();
        }
        $oldValues = [];
        foreach ($products as $product) {
            $oldValues[] = $product->getQuantityInStock();
        }
        $newValues = [];
        foreach ($oldValues as $k => $oldValue) {
            $newValues[$k] = $oldValue + $amountReturned[$k];
        }
        $this->validateAndApply($newValues, $items, $products);
    }

    /**
     * order finish action is disallowed if order.cart is empty
     * or if any cart.cartItem.quantity is <= 0
     */
    private function finish(Order $order): void
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
    }
}
