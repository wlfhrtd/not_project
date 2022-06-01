<?php

namespace App\Notification;

use App\Entity\Order;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\Button\InlineKeyboardButton;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\InlineKeyboardMarkup;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class OrderNotification extends Notification implements ChatNotificationInterface, EmailNotificationInterface
{
    private $order;
    private $orderUrl;

    /**
     * @param Order $order
     * @param string $orderUrl
     */
    public function __construct(Order $order, string $orderUrl)
    {
        $this->order = $order;
        $this->orderUrl = $orderUrl;

        if ($order->getStatus() === Order::STATUS_ORDER_IN_PROGRESS) {
            parent::__construct('New order added! Order id: ' . $order->getId());
        }

        if ($order->getStatus() === Order::STATUS_ORDER_FINISHED) {
            parent::__construct('Order finished! Order id: ' . $order->getId());
        }

        if ($order->getStatus() === Order::STATUS_ORDER_CANCELED) {
            parent::__construct('Order canceled! Order id: ' . $order->getId());
        }
    }

    public function asChatMessage(RecipientInterface $recipient, string $transport = null): ?ChatMessage
    {
        if ('telegram' !== $transport) {
            return null;
        }

        $message = ChatMessage::fromNotification($this, $recipient, $transport);
        $message->subject($this->getSubject());
        $message->options((new TelegramOptions())
            ->parseMode('MarkdownV2')
            ->disableWebPagePreview(true)
            ->disableNotification(false)
            ->replyMarkup((new InlineKeyboardMarkup())
                ->inlineKeyboard([
                    (new InlineKeyboardButton('View order'))
                        ->url($this->orderUrl),
                ])
            )
        );

        return $message;
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        if ($this->order->getStatus() === Order::STATUS_ORDER_FINISHED) {
            $this->importance(Notification::IMPORTANCE_URGENT);
            return ['email'];
        }

        // order.status==in_progress || order.status==canceled
        $this->importance(Notification::IMPORTANCE_HIGH);
        return ['chat/telegram'];
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient, $transport);
        $message->getMessage()
            ->htmlTemplate('emails/order_notification.html.twig')
            ->context(['order' => $this->order])
        ;

        return $message;
    }
}
