<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;

class Locker
{
    private $errors;
    private $backup; // contains string representation of discarded order entity if transaction failed

    private function setError(string $error, Order $order = null, Request $request = null): void
    {
        switch ($error) {
            case self::ERROR_ORDER_OPTIMISTIC_LOCK:
                $message = 'Entity version mismatch @@ Sorry, but someone has already changed this Order. '
                    . 'Your version: ' . $request->getSession()->get($order->getId()) . '; '
                    . 'Version in db: ' . $order->getCurrentVersion();
                $discarded = "$$$ Discarded data: \n" . $order->toLongString();
                break;
            case self::ERROR_ORDER_PESSIMISTIC_LOCK:
                $message = 'Transaction failed @@ ';
                $discarded = "$$$ Discarded data: \n" . $this->backup;
                break;
        }

        $this->errors[] = $message;
        $this->errors[] = $discarded;
    }

    const ERROR_ORDER_OPTIMISTIC_LOCK = 'Optimistic';
    const ERROR_ORDER_PESSIMISTIC_LOCK = 'Pessimistic';

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function updateOptimistic(Request $request, Order $order): void
    {
        $request->getContent() ?: $request->getSession()->set($order->getId(), $order->getCurrentVersion());
    }

    public function check(Request $request, Order $order): bool
    {
        if ($request->getSession()->get($order->getId()) != $order->getCurrentVersion()) {
            $this->setError(self::ERROR_ORDER_OPTIMISTIC_LOCK, $order, $request);
            return false;
        }
        return true;
    }

    public function pessimisticAdd(Order $order, OrderRepository $orderRepository, array $transaction): bool
    {
        $this->backup = $order->toLongString();

        try {
            $orderRepository->addPessimistic($order, $transaction);
        } catch (Exception $e) {
            $this->backup .= "\n" . $e->getMessage() . "\n";
            $this->setError(self::ERROR_ORDER_PESSIMISTIC_LOCK);
            return false;
        }
        return true;
    }

    public function pessimisticHide(Order $order, OrderRepository $orderRepository, array $transaction): bool
    {
        $this->backup = "";

        try {
            $orderRepository->hidePessimistic($order, $transaction);
        } catch (Exception $e) {
            $this->backup .= "\n" . $e->getMessage() . "\n";
            $this->setError(self::ERROR_ORDER_PESSIMISTIC_LOCK);
            return false;
        }
        return true;
    }
}
