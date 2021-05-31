<?php


namespace AppBundle\Tests\Lib\Subscription\Validator;


use AppBundle\Entity\Product;
use AppBundle\Entity\Store;
use AppBundle\Lib\Subscription\Form\SubscriptionRequestDto;
use AppBundle\Lib\Tools;
use AppBundle\Tests\EntityManagerAwareTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionRequestValidatorTest extends EntityManagerAwareTestCase
{
    public function testProductAndStoreNotSet() :void
    {
        $request = new SubscriptionRequestDto();
        $errors = $this->getValidatorService()->validate($request);

        static::assertCount(2, $errors);

        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors AS $error) {
            $property = $error->getPropertyPath();
            $message = $error->getMessage();

            static::assertContains($property, ['product', 'store']);
            static::assertContains($message, ['You must provide a store', 'You must provide a product']);
        }
    }

    public function testProductCountryDoesntMatchStoreLocation() :void
    {
        $de = $this->getCountryByName('DE');
        $ch = $this->getCountryByName('CH');

        $product = new Product();
        $product->setCountry($de);

        $store = new Store();
        $store->setCountry($ch);

        $request = new SubscriptionRequestDto();
        $request->setProduct($product);
        $request->setStore($store);

        $errors = $this->getValidatorService()->validate($request);

        static::assertCount(1, $errors);
        $error = $errors->get(0);

        static::assertEquals('The selected product is only available in DE', $error->getMessage());
    }

    public function testProductIsExpired() :void
    {
        $de = $this->getCountryByName('DE');

        $until = Tools::getLastDayDateTimeForMonth(new \DateTime());
        $product = new Product();
        $product->setCountry($de);
        $product->setAvailableUntil($until);

        $store = new Store();
        $store->setCountry($de);

        $request = new SubscriptionRequestDto();
        $request->setProduct($product);
        $request->setStore($store);

        $errors = $this->getValidatorService()->validate($request);

        static::assertCount(1, $errors);
        $error = $errors->get(0);

        static::assertEquals('The selected product is not available anymore', $error->getMessage());
    }

    public function testProductIsNotActive() :void
    {
        $de = $this->getCountryByName('DE');

        $start = Tools::getFirstDayDateTimeForMonth(new \DateTime());
        $start->add(new \DateInterval('P3M'));

        $until = Tools::getLastDayDateTimeForMonth($start);
        $until->add(new \DateInterval('P6M'));
        $product = new Product();
        $product->setCountry($de);
        $product->setAvailableFrom($start);
        $product->setAvailableUntil($until);

        $store = new Store();
        $store->setCountry($de);

        $start = Tools::getFirstDayDateForNextMonth(new \DateTime());
        $request = new SubscriptionRequestDto();
        $request->setProduct($product);
        $request->setStore($store);
        $request->setStart($start);

        $errors = $this->getValidatorService()->validate($request);

        static::assertCount(1, $errors);
        $error = $errors->get(0);

        static::assertEquals('The selected product is not available at the desired date', $error->getMessage());
    }

    public function testStartIsInThePast() :void
    {
        $de = $this->getCountryByName('DE');

        $start = Tools::getFirstDayDateTimeForMonth(new \DateTime());
        $start->add(new \DateInterval('P3M'));

        $until = Tools::getLastDayDateTimeForMonth($start);
        $until->add(new \DateInterval('P6M'));
        $product = new Product();
        $product->setCountry($de);
        $product->setAvailableFrom($start);
        $product->setAvailableUntil($until);
        $product->setMinSubscriptionTime(12);

        $store = new Store();
        $store->setCountry($de);

        $request = new SubscriptionRequestDto();
        $request->setProduct($product);
        $request->setStore($store);
        $request->setStart(new \DateTime('2021-01-01'));

        $errors = $this->getValidatorService()->validate($request);

        static::assertCount(1, $errors);
        $error = $errors->get(0);

        static::assertEquals('A new subscription can not start in the past', $error->getMessage());
    }

    public function testProductExpiresBeforeSubscriptionEnd() :void
    {
        $de = $this->getCountryByName('DE');

        $start = Tools::getFirstDayDateTimeForMonth(new \DateTime());
        $start->add(new \DateInterval('P2M'));

        $until = Tools::getLastDayDateTimeForMonth($start);
        $until->add(new \DateInterval('P6M'));
        $product = new Product();
        $product->setCountry($de);
        $product->setAvailableFrom($start);
        $product->setAvailableUntil($until);
        $product->setMinSubscriptionTime(12);

        $store = new Store();
        $store->setCountry($de);

        $request = new SubscriptionRequestDto();
        $request->setProduct($product);
        $request->setStore($store);

        $errors = $this->getValidatorService()->validate($request);

        static::assertCount(1, $errors);
        $error = $errors->get(0);

        static::assertEquals('You can not subscribe to the given product since it will expire before subscription end date', $error->getMessage());
    }

    public function testQuantityNotValid() :void
    {
        $de = $this->getCountryByName('DE');

        $start = Tools::getFirstDayDateTimeForMonth(new \DateTime());
        $start->add(new \DateInterval('P2M'));

        $until = Tools::getLastDayDateTimeForMonth($start);
        $until->add(new \DateInterval('P24M'));
        $product = new Product();
        $product->setCountry($de);
        $product->setAvailableFrom($start);
        $product->setAvailableUntil($until);
        $product->setMinSubscriptionTime(12);
        $product->setMinQuantity(100);

        $store = new Store();
        $store->setCountry($de);

        $request = new SubscriptionRequestDto();
        $request->setProduct($product);
        $request->setStore($store);
        $request->setQuantity(5);

        $errors = $this->getValidatorService()->validate($request);

        static::assertCount(1, $errors);
        $error = $errors->get(0);

        static::assertEquals('The requested quantity is lower than the minimum quantity (100)', $error->getMessage());
    }

    /**
     * @return ValidatorInterface
     */
    private function getValidatorService() :ValidatorInterface
    {
        return $this->getContainer()->get('validator');
    }
}