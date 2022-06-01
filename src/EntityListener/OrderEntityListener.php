<?php

namespace App\EntityListener;

use App\Entity\Order;
use App\Message\OrderMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderEntityListener
{
    private $bus;

    /**
     * @param MessageBusInterface $bus
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    // ACTION NEW
    public function prePersist(Order $order): void
    {
        /*
         * you might submit new order with products quantities as 0 (or even without any cart items at all)
         * so order.total would be 0 as well
         * that way default order.status will stay as 'draft'
         *
         * otherwise if any cartItem.quantity > 0 you will get order.total > 0
         * that way order.status will change to 'in_progress'
         */
        if ($order->getTotal() != 0) {
            $order->setStatus(Order::STATUS_ORDER_IN_PROGRESS);
        }
    }

    /*
     * we don't have generated ID in prePersist
     * so to do something with 'ready to action' entity
     * should listen to postPersist or later
     */
    public function postPersist(Order $order): void
    {
        /*
         * ignore order.status==draft; no notifications for order.status==draft
         * notify about order.status==in_progress only
         */
        if ($order->getStatus() === Order::STATUS_ORDER_IN_PROGRESS) {
            // send to queue, send telegram notification ~ 'new order added'
            $this->bus->dispatch(new OrderMessage($order->getId()));
        }
    }

    // TODO PREVENT ORDER FINISH ACTION IF ANY CART ITEM QUANTITY CONTAINS 0 (CONTROLLER? ENTITY LISTENER? ORDER VALIDATOR SERVICE?)
    // TODO PREVENT CART ITEMS REMOVE AND SETTING BELOW ==1 IF ORDER.STATUS==IN_PROGRESS AND HIGHER: FORCE TO CANCEL ORDER AND CREATE NEW ORDER IF NEEDED

    // EDIT, FINISH, CANCEL ACTIONS
    public function preUpdate(Order $order): void
    {
        // ORDER.STATUS==DRAFT CASES

        // changes made (not to product quantities), but it is still in 'draft' state; no actions required
        if ($order->getTotal() == 0 && $order->getStatus() === Order::STATUS_ORDER_DRAFT) {
            return;
        }
        // order.status==draft >> order.status==in_progress case
        if ($order->getTotal() != 0 && $order->getStatus() === Order::STATUS_ORDER_DRAFT) {
            $order->setStatus(Order::STATUS_ORDER_IN_PROGRESS);
            // send to queue, send telegram notification ~ 'new order added'
            $this->bus->dispatch(new OrderMessage($order->getId()));
            return;
        }

        // ORDER.STATUS==IN_PROGRESS CASE

        // changes made but without any order.status changes we don't need any notifications
        if ($order->getStatus() === Order::STATUS_ORDER_IN_PROGRESS) {
            return;
        }

        // ORDER.STATUS==FINISHED CASE
        // just recently finished order; export order to document will fire update event again due to setSpreadsheetFilename() - don't need another email
        if ($order->getStatus() === Order::STATUS_ORDER_FINISHED && $order->getSpreadsheetFilename() == null) {
            // send to queue, send email notification with link to order(show view)
            $this->bus->dispatch(new OrderMessage($order->getId()));
            return;
        }

        // ORDER.STATUS==CANCELED CASE
        if ($order->getStatus() === Order::STATUS_ORDER_CANCELED) {
            // send to queue, send telegram notification ~ 'order canceled'
            $this->bus->dispatch(new OrderMessage($order->getId()));
        }
    }
}
