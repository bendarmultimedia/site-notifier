<?php

namespace App\Notifier;

use App\Message\MessageInterface;
use App\Telegram\API;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class TelegramNotifier implements NotifierInterface
{
    public const DEFAULT_OPTIONS = [
        'botToken'     => null,
    ];

    public function __construct(private array $options = [], private ?API $api = null) {
        $this->configure($options);
        if (null === $this->api) {
            $this->api = new API();
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function notify(MessageInterface $message): bool
    {
        $this->api->setCurrentBotToken($this->options['bot_token']);
        $response = $this->api->sendMessage($message->getBody(), $message->getTarget());

        return $response->getStatusCode() === 200;
    }

    public function setBotToken(string $token): void
    {
        $this->options['botToken'] = $token;
    }

    public function configure(?array $options): array
    {
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);
        return $this->options;
    }
}