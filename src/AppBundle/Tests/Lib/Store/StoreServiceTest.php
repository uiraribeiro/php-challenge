<?php


namespace AppBundle\Tests\Lib\Store;


use AppBundle\Lib\Store\StoreCreatedEvent;
use AppBundle\Lib\Store\StoreService;
use AppBundle\Lib\Store\StoreUpdatedEvent;
use AppBundle\Lib\ValidationException;
use AppBundle\Tests\EntityManagerAwareTestCase;

class StoreServiceTest extends EntityManagerAwareTestCase
{
    public static function setUpBeforeClass() :void
    {
        static::bootstrapDatabase();
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCreate():void
    {
        $country  = $this->getCountryByName('DE');
        $customer = $this->getCustomerByName('customer_2');
        $store = StoreService::createPrototype($customer, new \DateTime('2021-01-01'));

        $service = $this->getContainer()->get('app.store.service');

        try {
            $service->create($store);
            static::fail('Creating a store without a country should not work');

        } catch (ValidationException $ve) {
            $errors = $ve->getValidationErrors();
            static::assertCount(3, $errors);
            foreach ($errors AS $error) {
                $property = $error->getPropertyPath();
                $message = $error->getMessage();
                switch ($property) {
                    case 'country':
                        static::assertEquals('You must provide a country', $message);
                    break;
                    case 'name':
                        static::assertEquals('You must provide a name', $message);
                    break;
                    case 'description':
                        static::assertEquals('You must provide a description', $message);
                    break;
                    default:
                        static::fail('Unknown error: '.$property.' '.$message);
                    break;
                }
            }
        }

        try {
            $store->setCountry($country);
            $store->setName('customer_2_test_store');
            $store->setDescription('description');
            $service->create($store);

            $messages = StoreCreatedEvent::$messages;
            static::assertCount(1, $messages);


        } catch (\Exception $e) {
            static::fail('Strange, this should have worked');
        }
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testUpdate() :void
    {
        $store = $this->getStoreByName('customer_2_test_store');
        $service = $this->getContainer()->get('app.store.service');

        try {
            $store->setName('');
            $store->setDescription('');
            $service->update($store);
            static::fail('Updating a store without name and description should not work');

        } catch (ValidationException $ve) {
            $errors = $ve->getValidationErrors();
            static::assertCount(2, $errors);
            foreach ($errors AS $error) {
                $property = $error->getPropertyPath();
                $message = $error->getMessage();
                switch ($property) {
                    case 'name':
                        static::assertEquals('You must provide a name', $message);
                        break;
                    case 'description':
                        static::assertEquals('You must provide a description', $message);
                        break;
                    default:
                        static::fail('Unknown error: '.$property.' '.$message);
                        break;
                }
            }
        }

        $em = $this->getEntityManager();
        $em->refresh($store);

        try {
            $service->update($store);

            $messages = StoreUpdatedEvent::$messages;
            static::assertCount(1, $messages);


        } catch (\Exception $e) {
            static::fail('Strange, this should have worked');
        }

        $em->remove($store);
        $em->flush();
    }
}