<?php


namespace AppBundle\Lib;


use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Class MailerService, this is just a dummy mailer service which is used to emulate mailing
 * @package AppBundle\Lib
 */
class MailerService
{
    use LoggerAwareTrait;

    public function __construct()
    {
        $this->setLogger(new NullLogger());
    }

    /**
     * @param string $email
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function send(string $email, string $subject, string $body) :string
    {
        $message = sprintf('Would have send email to: %s with subject: %s and body: %s', $email, $subject, $body);

        $this->logger->info($message);

        return $message;
    }


}