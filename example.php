<?php
require_once "vendor/autoload.php";

use App\Message\EmailMessage;
use App\Message\TelegramMessage;
use App\SiteNotifier;

$config = [
    'site_to_check' => 'https://example.test',
    'telegram_options' => [
        'bot_token' => 'CHANGE_ME',
        'chat_id' => 'CHANGE_ME'
    ],
    "mailer_options" => [
        'Host'          => 'CHANGE_ME',
        'SMTPAuth'      => true,
        'Username'      => 'CHANGE_ME',
        'Password'      => 'CHANGE_ME',
        'Port'          => 587,
        'From'          => 'Site Notifier'
    ]
];
$currentConfig = $config;

$siteNotifier = new SiteNotifier();
$searchElement = "#reservation-dates-container";
$messageText = "📅 On the page: {$currentConfig['site_to_check']}\n exists element '{$searchElement}'! 🎉";


// === Telegram notification ===
$messageTelegram = new TelegramMessage($messageText, "", $currentConfig['telegram_options']['chat_id']);

$siteNotifier->setUp(
    $currentConfig['site_to_check'],
    $searchElement,
    $messageTelegram,
    0
)->setTelegramBotToken($currentConfig['telegram_options']['bot_token'])->run();
// ========================

//=== Email notification ===
$messageEmail = new EmailMessage($messageText, "SiteNotifier", "example@email.test");

$siteNotifier->reset()->setUp(
    $currentConfig['site_to_check'],
    $searchElement,
    $messageEmail,
    0
)->setMailerConfig($currentConfig['mailer_options'])->run();
// ========================