<?php


namespace AppBundle\Tests\Lib\Store\Form;


use AppBundle\Entity\Country;
use AppBundle\Entity\Store;
use AppBundle\Lib\Store\Form\CreateUpdateFormType;
use AppBundle\Tests\EntityManagerAwareTestCase;
use Symfony\Component\Form\FormError;

class CreateUpdateFormTest extends EntityManagerAwareTestCase
{
    public function testCreate() :?int
    {
        $customer = $this->getCustomerByName('customer_1');
        if (!$customer) {
            static::fail('Cannot find customer for test');
        }

        $store = new Store();
        $store->setCustomer($customer);
        $store->setStartDate(new \DateTime());
        $form = $this->createForm(CreateUpdateFormType::class, $store);

        $data = [
            'name' => 'customer_1_store_create_test',
            'description' => 'Test description',
            'country' => 'DE'
        ];

        $form->submit($data);
        static::assertTrue($form->isValid());

        /**
         * @var Store $submitted
         */
        $submitted = $form->getData();
        static::assertInstanceOf(Country::class, $submitted->getCountry());
        static::assertEquals('DE', $submitted->getCountryCode());
        static::assertEquals($data['name'], $submitted->getName());
        static::assertEquals($data['description'], $submitted->getDescription());

        $em = $this->getEntityManager();
        $em->persist($submitted);
        $em->flush();

        return $submitted->getId();
    }

    /**
     * @param int $storeId
     * @depends testCreate
     */
    public function testUpdate(int $storeId) :void
    {
        $em = $this->getEntityManager();
        $store = $em->getRepository(Store::class)->find($storeId);

        if (!$store) {
            static::fail('Cannot find store to update');
        }

        $form = $this->createForm(CreateUpdateFormType::class, $store);

        $data = [
            'name' => 'customer_1_store_create_test_updated',
            'description' => 'Test description updated',
            'country' => 'CH'
        ];

        $form->submit($data);
        static::assertTrue($form->isValid());

        /**
         * @var Store $submitted
         */
        $submitted = $form->getData();
        static::assertInstanceOf(Country::class, $submitted->getCountry());
        static::assertEquals('CH', $submitted->getCountryCode());
        static::assertEquals($data['name'], $submitted->getName());
        static::assertEquals($data['description'], $submitted->getDescription());

        $em->remove($submitted);
        $em->flush();
    }

    public function testCountryChangeNotAllowed() :void
    {
        $store = $this->getStoreByName('customer_1_store_1');
        if (!$store) {
            static::fail('Cannot find store to update');
        }

        static::assertNotCount(0, $store->getSubscriptions());

        $form = $this->createForm(CreateUpdateFormType::class, $store);

        $data = [
            'name' => $store->getName(),
            'description' => 'description',
            'country' => 'CH'
        ];

        $form->submit($data, false);
        static::assertFalse($form->isValid());
        $errors = $form->getErrors(true, true);
        static::assertCount(1, $errors);

        /**
         * @var FormError $error
         */
        foreach ($errors AS $error) {
            $s = $error->getCause();
            static::assertEquals('data.country', $s->getPropertyPath());
            static::assertEquals('Country cannot be changed', $s->getMessage());
        }

    }
}