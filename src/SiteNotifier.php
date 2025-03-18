<?php

namespace App;

use App\Marker\NotificationMarker;
use App\Message\EmailMessage;
use App\Message\MessageInterface;
use App\Message\TelegramMessage;
use App\Notifier\BaseNotifier;
use App\Scraper\WebScraper;
use RuntimeException;

final class SiteNotifier
{
    private int $daysToWaitUntilNextNotification = 0;

    private string $url;
    private string $cssSelector;
    private MessageInterface $message;

    private ?string $telegramBotToken = null;
    private ?array $mailerConfig = null;
    private bool $forceRun = false;
    private BaseNotifier $notifier;
    private NotificationMarker $notificationMarker;
    private WebScraper $webScraper;


    public function __construct(    )
    {
        $this->notifier =  new BaseNotifier();
        $this->notificationMarker =  new NotificationMarker();
        $this->webScraper =  new WebScraper();
    }

    public function contentOnSiteExists(string $cssSelector, string $url): bool
    {
        return $this->webScraper->contentExists($cssSelector, $url);
    }

    public function shouldRun(): bool
    {
        if ($this->forceRun) {
            return true;
        }
        $timeFromLastNotification = $this->notificationMarker->timeFromLastNotification();
        return (null === $timeFromLastNotification || $timeFromLastNotification->days >= $this->daysToWaitUntilNextNotification);
    }

    public function setUp(
        string $url,
        string $cssSelector,
        MessageInterface $message,
        int $daysToWaitUntilNextNotification = 0,
    ): self
    {

        $this->url = $url;
        $this->cssSelector = $cssSelector;
        $this->message = $message;
        $this->daysToWaitUntilNextNotification = $daysToWaitUntilNextNotification;
        return $this;
    }

    public function run(): void
    {
        if ($this->shouldRun()) {
            $this->validate();
            if ($this->contentOnSiteExists($this->cssSelector, $this->url)) {
                if ($this->notifier->notify($this->message)) {
                    $this->notificationMarker->markAsNotified();
                    $this->forceRun = false;
                }
            }
        }
    }

    public function setTelegramBotToken(string $token): self
    {
        $this->telegramBotToken = $token;
        $this->notifier->setTelegramBotTokenForAPI($this->telegramBotToken);
        return $this;
    }

    private function validate(): void
    {
        if ($this->message instanceof TelegramMessage && null === $this->telegramBotToken) {
                throw new RuntimeException("Telegram bot token is required, use setTelegramBotToken() method.");
        }
        if ($this->message instanceof EmailMessage && null === $this->mailerConfig) {
                throw new RuntimeException("Mailer config is required, use configureMailer() method.");
        }
    }

    public function setMailerConfig(array $mailerConfig): self
    {
        $this->mailerConfig = $mailerConfig;
        $this->notifier->configure(['mailer_options' => $this->mailerConfig]);
        return $this;
    }
    public function reset(): self
    {
        $this->notifier->reset();
        $this->forceRun = true;
        return $this;
    }

}