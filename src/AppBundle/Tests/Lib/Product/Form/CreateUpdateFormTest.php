<?php


namespace AppBundle\Tests\Lib\Product\Form;


use AppBundle\Entity\Product;
use AppBundle\Lib\Product\Form\CreateUpdateFormType;
use AppBundle\Tests\EntityManagerAwareTestCase;

class CreateUpdateFormTest extends EntityManagerAwareTestCase
{
    public function testCreate() :void
    {
        $form = $this->createForm(CreateUpdateFormType::class, new Product());
        $required = ['item', 'country', 'notes', 'price', 'vat', 'available_from',
            'available_from', 'min_subscription_time', 'min_quantity', 'shipping_costs'];

        foreach ($required AS $key) {
            static::assertTrue($form->has($key));
        }

        $data = [
            'item' => 1,
            'country' => 'DE',
            'notes' => '',
            'price' => 100,
            'vat' => '7.15',
            'available_from' => '2021-01-01',
            'available_until' => '2021-12-01',
            'min_subscription_time' => 1,
            'min_quantity' => 100,
            'shipping_costs' => [
                ['from' => 0, 'costs' => 6],
                ['from' => 100, 'costs' => 6],
            ]
        ];

        $form->submit($data, false);

        /**
         * @var Product $submitted
         */
        $submitted = $form->getData();
        static::assertInstanceOf(Product::class, $submitted);

    }

}