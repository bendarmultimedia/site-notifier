<?php

namespace App\Notifier;

use App\Message\EmailMessage;
use App\Message\MessageInterface;
use App\Message\TelegramMessage;

final class BaseNotifier implements NotifierInterface
{
    private ?NotifierInterface $notifier = null;

    private array $config = [];

    public function notify(MessageInterface $message): bool
    {
        if ($this->notifier === null) {
            $this->setNotifier($message);
        }
        return $this->notifier->notify($message);
    }

    private function setNotifier(MessageInterface $message): void
    {
        if ($message instanceof EmailMessage) {
            $this->notifier = new EmailNotifier($this->config['mailer_options']);
        }
        if ($message instanceof TelegramMessage) {
            $this->notifier = new TelegramNotifier($this->config['telegram_options']);
        }
    }

    public function setTelegramBotTokenForAPI(?string $telegramBotToken): void
    {
        $this->config['telegram_options']['bot_token'] = $telegramBotToken;
    }

    public function configure(?array $options): array
    {
        if (array_key_exists('telegram_options', $options)) {
            $this->config['telegram_options'] = $options['telegram_options'];
        }
        if (array_key_exists('mailer_options', $options)) {
            $this->config['mailer_options'] = $options['mailer_options'];
        }
        return $this->config;
    }

    public function reset(): void
    {
        $this->notifier = null;
    }


}