<?php


namespace AppBundle\Lib\Subscription\Validator;


use AppBundle\Entity\Product;
use AppBundle\Entity\Store;
use AppBundle\Lib\Subscription\Form\SubscriptionRequestDto;
use AppBundle\Lib\Tools;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SubscriptionRequestValidValidator extends ConstraintValidator
{

    /**
     * @throws \Exception
     */
    public function validate($value, Constraint $constraint) :void
    {
        if (!$value instanceof SubscriptionRequestDto) {
            throw new \BadMethodCallException('You must pass an instance of '.SubscriptionRequestDto::class);
        }

        $context = $this->context;
        // First check if product and store are set
        if (!$value->getStore() instanceof Store) {
            $context->buildViolation('You must provide a store')
                ->atPath('store')->addViolation();
        }
        $store = $value->getStore();

        if (!$value->getProduct() instanceof Product) {
            $context->buildViolation('You must provide a product')
                ->atPath('product')->addViolation();
        }

        $product = $value->getProduct();

        if ($product === null || $store === null) {
            // Abort since it makes no sense to check any further
            return;
        }

        // Then check if product is available for the store
        if ($product->getCountryCode() !== $store->getCountryCode()) {
            $context->buildViolation('The selected product is only available in {{ country }}')
                ->setParameter('{{ country }}', $product->getCountryCode())
                ->atPath('store')->addViolation();

            // Abort since it makes no sense to check any further
            return;
        }

        // Then check if product is still available
        if ($product->isExpired()) {
            $context->buildViolation('The selected product is not available anymore')
                ->atPath('product')->addViolation();

            return;
        }

        // The user is not required to provide a start date, if not set it defaults either to the
        // first of the next month or the start date of the product.
        $start = $value->getStartDate();

        $nextFirst = Tools::getFirstDayDateForNextMonth(new \DateTime());

        if ($start < $nextFirst) {
            $context->buildViolation('A new subscription can not start in the past')
                ->atPath('start')->addViolation();

            return;
        }

        if ($start < $product->getAvailableFrom()) {
            $context->buildViolation('The selected product is not available at the desired date')
                ->atPath('product')->addViolation();

            return;
        }



        // End date is automatically calculated from the start date and the minimum subscription time
        $end = $value->getEndDate();
        if ($product->isExpired($end)) {
            $context->buildViolation('You can not subscribe to the given product since it will expire before subscription end date')
                ->atPath('product')->addViolation();

            return;
        }

        $quantity = $value->getQuantity();
        if ($quantity < $product->getMinQuantity()) {
            $context->buildViolation('The requested quantity is lower than the minimum quantity ({{ min }})')
                ->setParameter('{{ min }}', $product->getMinQuantity())
                ->atPath('product')->addViolation();

            return;
        }
    }
}