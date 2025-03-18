<?php

namespace App\Notifier;

use App\Exception\BadConfigurationException;
use App\Message\MessageInterface;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


final class EmailNotifier implements NotifierInterface
{
    private bool $isConfigured = false;
    public const MAILER_OPTIONS = [
        'SMTPDebug'     => SMTP::DEBUG_OFF,
        'Host'          => 'server',
        'SMTPAuth'      => true,
        'Username'      => 'email@test.com',
        'Password'      => 'CHANGE ME',
        'SMTPSecure'    => PHPMailer::ENCRYPTION_STARTTLS,
        'Port'          => 587,
        'CharSet'       => PHPMailer::CHARSET_UTF8,
    ];

    private PHPMailer $mailer;
    public function __construct(?array $options = null) {
        $this->mailer = new PHPMailer();
        $options = array_merge(self::MAILER_OPTIONS, $options);
        $this->configure($options);
    }

    private function setUpMailer(array $options): void
    {
        $this->mailer->isSMTP();
        $this->mailer->SMTPDebug  = $options['SMTPDebug'];
        $this->mailer->Host       = $options['Host'];
        $this->mailer->SMTPAuth   = $options['SMTPAuth'];
        $this->mailer->Username   = $options['Username'];
        $this->mailer->Password   = $options['Password'];
        $this->mailer->SMTPSecure = $options['SMTPSecure'];
        $this->mailer->Port       = $options['Port'];
        $this->mailer->CharSet    = $options['CharSet'];
        if(isset($options['From'])){
            try {
                $this->mailer->setFrom($options['Username'], $options['From']);
            } catch (Exception $e) {}
        }
    }

    /**
     * @throws BadConfigurationException
     */
    public function notify(MessageInterface $message): bool
    {
        if (!$this->isConfigured) {
            throw new BadConfigurationException($this);
        }

        try {
            $this->setEmailOptions($message);
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function setFrom(string $email, string $name): bool
    {
        return $this->mailer->setFrom($email, $name);
    }

    /**
     * @throws Exception
     */
    private function setEmailOptions(MessageInterface $message): void
    {
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $message->getTitle();
        $this->mailer->Body = $message->getBody();
        $this->mailer->addAddress($message->getTarget());
    }

    public function configure(?array $options): array
    {
        $this->setUpMailer($options);
        $this->isConfigured = true;
        return $options;
    }


}