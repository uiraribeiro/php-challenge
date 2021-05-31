<?php


namespace AppBundle\Lib\EventSubscriber;


use AppBundle\Lib\MailerService;
use AppBundle\Lib\Store\StoreCreatedEvent;
use AppBundle\Lib\Store\StoreUpdatedEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailingSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerService
     */
    protected $mailer;

    use LoggerAwareTrait;

    public function __construct(MailerService $mailer)
    {
        $this->mailer = $mailer;
        $this->logger = new NullLogger();
    }

    public static function getSubscribedEvents() :array
    {
        return [
            StoreCreatedEvent::NAME => 'onStoreCreated',
            StoreUpdatedEvent::NAME => 'onStoreUpdated'];
    }

    public function onStoreCreated(StoreCreatedEvent $event) :void
    {
        $store = $event->getStore();
        $email = 'dummy@notvalid.com';
        $subject = sprintf('created store: %s', $store->getName());
        $body = sprintf('Created new store: %s %s in country: %s',
            $store->getName(), $store->getDescription(), $store->getCountryCode());

        $message = $this->mailer->send($email, $subject, $body);

        $event->addMessage($message);

        $this->logger->info('Send out message on store.created event');
    }

    public function onStoreUpdated(StoreUpdatedEvent $event) :void
    {
        $store = $event->getStore();
        $email = 'dummy@notvalid.com';
        $subject = sprintf('updated store: %s', $store->getName());
        $body = sprintf('Updated new store: %s %s in country: %s',
            $store->getName(), $store->getDescription(), $store->getCountryCode());

        $message = $this->mailer->send($email, $subject, $body);

        $event->addMessage($message);

        $this->logger->info('Send out message on store.updated event');
    }
}