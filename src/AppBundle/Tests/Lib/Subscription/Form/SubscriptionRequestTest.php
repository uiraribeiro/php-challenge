<?php


namespace AppBundle\Tests\Lib\Subscription\Form;


use AppBundle\Entity\Subscription;
use AppBundle\Lib\Subscription\Form\SubscriptionRequestDto;
use AppBundle\Lib\Subscription\Form\SubscriptionRequestFormType;
use AppBundle\Lib\Tools;
use AppBundle\Tests\EntityManagerAwareTestCase;

class SubscriptionRequestTest extends EntityManagerAwareTestCase
{
    public function testForm()
    {
        $request = new SubscriptionRequestDto();
        $form = $this->createForm(SubscriptionRequestFormType::class, $request);

        $start = Tools::getFirstDayDateForNextMonth(new \DateTime());
        $data = [
            'store' => 1,
            'product' => 1,
            'start' => $start->format('Y-m-d'),
            'quantity' => 100,
            'recurring' => 0
        ];
        $form->submit($data);

        $form->isValid();
        $errors = $form->getErrors(true, true);

        static::assertTrue($form->isValid());

        /**
         * @var SubscriptionRequestDto $submitted
         */
        $submitted = $form->getData();
        static::assertEquals(1, $submitted->getStore()->getId());
        static::assertEquals(1, $submitted->getProduct()->getId());
        static::assertEquals($start->format('Y-m-d'), $submitted->getStart()->format('Y-m-d'));
        static::assertEquals(100, $submitted->getQuantity());
        static::assertFalse($submitted->isRecurring());

        $subscription = $submitted->toSubscription();
        static::assertEquals(1, $subscription->getStore()->getId());
        static::assertEquals(1, $subscription->getProduct()->getId());
        static::assertEquals($start->format('Y-m-d'), $subscription->getStartDate()->format('Y-m-d'));
        static::assertEquals(100, $subscription->getQuantity());
        static::assertFalse($subscription->isRecurring());

    }
}