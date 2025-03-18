# Site Notifier
The class **SiteNotifier** parse response from URL and checks it contains any node by CSS selector.
If yes, it sends notification which is determined by Message:
- EmailNotification
- TelegramNotification

## Configuration
Here is an example of configuration:
```
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
```
## Telegram notification
So, if you want to send a notification for Telegram chat you can create a message:
```
$messageTelegram = new TelegramMessage("Sample message", "Title", $config['telegram_options']['chat_id']);
```
And then configure the notifier object:
```
$siteNotifier = new SiteNotifier();
$daysToWaitForNextCheck = 0;
$siteNotifier->setUp(
    $config['site_to_check'],
    "#idOfNode",
    $messageTelegram,
    $daysToWaitForNextCheck
)->setTelegramBotToken($config['telegram_options']['bot_token'])->run();
```
## Email notification
For email messages here is an example:

```
$messageEmail = new EmailMessage("Sample message", "Title", "example@email.test");

$siteNotifier = new SiteNotifier();
$daysToWaitForNextCheck = 0;
$siteNotifier->setUp(
    $config['site_to_check'],
    "#idOfNode",
    $messageTelegram,
    $daysToWaitForNextCheck
)->setTelegramBotToken($config['telegram_options']['bot_token'])->run();
```
If you want to use the same object for sending, you need to reset it before use:
``$siteNotifier->reset();``

For web scrapping class uses:
- guzzlehttp/guzzle 
- symfony/dom-crawler
- symfony/css-selector

And for email notification it uses:
- phpmailer/phpmailer

Feel free to use and fork.