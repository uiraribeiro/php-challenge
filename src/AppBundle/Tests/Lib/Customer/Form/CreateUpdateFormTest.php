<?php


namespace AppBundle\Tests\Lib\Customer\Form;


use AppBundle\Entity\Customer;
use AppBundle\Lib\Customer\Form\CreateUpdateFormType;
use AppBundle\Tests\EntityManagerAwareTestCase;

class CreateUpdateFormTest extends EntityManagerAwareTestCase
{
    public function testForm() :void
    {
        $customer = new Customer();
        $form = $this->createForm(CreateUpdateFormType::class, $customer);

        $data = [
            'name' => 'bogus'
        ];
        $form->submit($data);

        /**
         * @var Customer $submitted
         */
        $submitted = $form->getData();
        static::assertEquals('bogus', $submitted->getName());
        static::assertTrue($form->isValid());

        $form = $this->createForm(CreateUpdateFormType::class, $customer);

        $data = [
            'name' => ''
        ];
        $form->submit($data);

        /**
         * @var Customer $submitted
         */
        $submitted = $form->getData();
        static::assertEquals('', $submitted->getName());
        static::assertFalse($form->isValid());
    }

}