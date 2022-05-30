<?php

namespace App\Notification;

use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class OrderNotification extends Notification implements ChatNotificationInterface
{
    private $orderId;

    /**
     * @param int $orderId
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;

        parent::__construct('New order added! Order id: ' . $orderId);
    }


    public function asChatMessage(RecipientInterface $recipient, string $transport = null): ?ChatMessage
    {
        if ('telegram' !== $transport) {
            return null;
        }

        $message = ChatMessage::fromNotification($this, $recipient, $transport);
        $message->subject($this->getSubject());
        $message->options((new TelegramOptions())
            // ->chatId('571585735')
            ->parseMode('MarkdownV2')
            ->disableWebPagePreview(true)
            ->disableNotification(false)
        );

        return $message;
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        return ['chat/telegram'];
    }
}
