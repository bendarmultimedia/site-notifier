<?php

namespace App\Notifier;


use App\Message\MessageInterface;

interface NotifierInterface
{
    public function notify(MessageInterface $message): bool;
    public function configure(?array $options): array;
}