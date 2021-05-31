<?php


namespace AppBundle\Lib\Subscription\Form;


use AppBundle\Entity\Subscription;
use Symfony\Component\Validator\Constraints as Assert;

class SubscriptionCancellationRequestDto
{
    /**
     * @var Subscription|null
     */
    private $subscription;

    /**
     * @Assert\NotBlank(message="You must provide a reason")
     * @var string|null
     */
    private $reason = '';

    /**
     * @var int|null
     */
    private $confirm;

    /**
     * @var \DateTime|null
     */
    private $atDate;

    /**
     * @return Subscription|null
     */
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    /**
     * @param Subscription|null $subscription
     * @return SubscriptionCancellationRequestDto
     */
    public function setSubscription(?Subscription $subscription): SubscriptionCancellationRequestDto
    {
        $this->subscription = $subscription;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     * @return SubscriptionCancellationRequestDto
     */
    public function setReason(?string $reason): SubscriptionCancellationRequestDto
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getConfirm(): ?int
    {
        return $this->confirm;
    }

    /**
     * @param int|null $confirm
     * @return SubscriptionCancellationRequestDto
     */
    public function setConfirm(?int $confirm): SubscriptionCancellationRequestDto
    {
        $this->confirm = $confirm;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAtDate(): ?\DateTime
    {
        return $this->atDate;
    }

    /**
     * @param \DateTime|null $atDate
     * @return SubscriptionCancellationRequestDto
     */
    public function setAtDate(?\DateTime $atDate): SubscriptionCancellationRequestDto
    {
        $this->atDate = $atDate;

        return $this;
    }
}