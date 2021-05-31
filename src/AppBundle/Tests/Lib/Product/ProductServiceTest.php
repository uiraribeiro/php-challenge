<?php


namespace AppBundle\Tests\Lib\Product;


use AppBundle\Entity\Product;
use AppBundle\Lib\Tools;
use AppBundle\Lib\ValidationException;
use AppBundle\Tests\EntityManagerAwareTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class ProductServiceTest extends EntityManagerAwareTestCase
{
    public function testValidate() :void
    {
        /**
         * @var ValidatorInterface $validator
         */
        $validator = $this->getContainer()->get('validator');

        $product = new Product();

        /**
         * @var ConstraintViolationList $errors
         */
        $errors = $validator->validate($product);

        static::assertNotCount(0, $errors);

        $expected = ['price', 'availableFrom', 'availableUntil', 'notes', 'item', 'country'];
        $actual = [];

        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors AS $error) {
            $actual[] = $error->getPropertyPath();
        }

        static::assertCount(count($expected), $actual);
        $diff = array_diff($expected, $actual);
        static::assertCount(0, $diff);

        $product = new Product();
        $product->setAvailableFrom(new \DateTime('2021-01-01'));
        $product->setAvailableUntil(new \DateTime('2020-01-01'));

        /**
         * @var ConstraintViolationList $errors
         */
        $errors = $validator->validate($product);

        $until = Tools::getErrorsByPath($errors, 'availableUntil');
        static::assertCount(1, $until);
        /**
         * @var ConstraintViolation $untilError
         */
        $untilError = $until[0];
        static::assertEquals('end date must be greater than start date', $untilError->getMessage());

        $product = new Product();
        $product->setAvailableFrom(new \DateTime('2021-01-01'));
        $product->setAvailableUntil(new \DateTime('2021-01-31'));
        $product->setMinSubscriptionTime(3);

        /**
         * @var ConstraintViolationList $errors
         */
        $errors = $validator->validate($product);

        $until = Tools::getErrorsByPath($errors, 'availableUntil');
        static::assertCount(1, $until);
        /**
         * @var ConstraintViolation $untilError
         */
        $untilError = $until[0];
        static::assertEquals('end date must be greater than min subscription period', $untilError->getMessage());

        $costs = [
            ['from' => 1],
            ['from' => 100, 'costs' => 100],
            ['from' => 100, 'costs' => 100],
        ];

        $product->setShippingCosts($costs);
        /**
         * @var ConstraintViolationList $errors
         */
        $errors = $validator->validate($product);

        $costs_missing = Tools::getErrorsByPath($errors, 'shippingCosts[0]');
        static::assertCount(1, $costs_missing);
        /**
         * @var ConstraintViolation $untilError
         */
        $costs_missing_error = $costs_missing[0];
        static::assertEquals('you must provide a costs value', $costs_missing_error->getMessage());

        $duplicate = Tools::getErrorsByPath($errors, 'shippingCosts[2]');
        static::assertCount(1, $duplicate);
        /**
         * @var ConstraintViolation $duplicate
         */
        $duplicate_error = $duplicate[0];
        static::assertEquals('duplicate scale', $duplicate_error->getMessage());
    }

    public function testCreate() :int
    {
        $item = $this->getItemByName('boskop');
        $country = $this->getCountryByName('DE');

        $availableFrom = Tools::getFirstDayDateTimeForMonth(new \DateTime());
        $availableUntil = clone $availableFrom;
        $availableUntil->add(new \DateInterval('P24M'));

        $product = new Product();
        $product->setItem($item);
        $product->setCountry($country);
        $product->setPrice(100);
        $product->setMinSubscriptionTime(12);
        $product->setMinQuantity(10);
        $product->setAvailableFrom($availableFrom);
        $product->setAvailableUntil($availableUntil);
        $product->setNotes('some notes');

        $service = $this->getContainer()->get('app.product.service');

        try {
            $newProduct = $service->create($product);
            static::assertNotNull($newProduct->getId());

            return $newProduct->getId();

        } catch (ValidationException $ve) {
            static::fail();
        }
    }

    /**
     * @param int $productId
     * @depends testCreate
     */
    public function testUpdate(int $productId) :void
    {
        $product = $this->getEntityManager()->getRepository(Product::class)->find($productId);

        if (!$product) {
            static::fail('cannot find product');
        }

        $product->setPrice(200);
        $service = $this->getContainer()->get('app.product.service');

        try {
            $product = $service->update($product);
            static::assertEquals(200, $product->getPrice());

        } catch (ValidationException $ve) {
            static::fail();
        }
    }

}