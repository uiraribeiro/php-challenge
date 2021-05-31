<?php


namespace AppBundle\Lib\Subscription;


use AppBundle\Entity\Subscription;
use AppBundle\Lib\AbstractService;
use AppBundle\Lib\Subscription\Form\SearchDto;
use AppBundle\Lib\Subscription\Form\SubscriptionCancellationRequestDto;
use AppBundle\Lib\Subscription\Form\SubscriptionRequestDto;
use AppBundle\Lib\ValidationException;

class SubscriptionService extends AbstractService
{
    protected function getRepository(): string
    {
        return Subscription::class;
    }

    /**
     * @param SubscriptionRequestDto $request
     * @return Subscription
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function create(SubscriptionRequestDto $request) :Subscription
    {
        $errors = $this->getValidator()->validate($request);

        if (0 !== count($errors)) {
            throw new ValidationException('Subscription request is not valid', $errors);
        }

        $subscription = $request->toSubscription();

        $em = $this->getEntityManager();
        $em->persist($subscription);
        $em->flush();

        $em->refresh($subscription);

        // Propagate SubscriptionCreatedEvent, the subscription specific subscribers are located in the Subscribers folder in this directory
        // or in Lib/EventSubscriber and configured in /app/config/services.yml
        $event = new SubscriptionCreatedEvent($subscription);
        $this->getDispatcher()->dispatch(SubscriptionCreatedEvent::NAME, $event);
        $this->getLogger()->info("Created subscription for store: ".$subscription->getStore()->getName());

        // refresh to get all stuff that might have been added
        $em->refresh($subscription);

        return $subscription;
    }

    /**
     * @param SubscriptionCancellationRequestDto $request
     * @return Subscription
     * @throws ValidationException
     */
    public function cancel(SubscriptionCancellationRequestDto $request) :Subscription
    {
        $errors = $this->getValidator()->validate($request);

        if (0 !== count($errors)) {
            throw new ValidationException('Subscription request is not valid', $errors);
        }

        $subscription = $request->getSubscription();
        // Do stuff, see user story for details

        return $subscription;
    }
}