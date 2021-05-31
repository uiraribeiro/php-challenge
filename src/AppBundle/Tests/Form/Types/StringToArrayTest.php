<?php


namespace AppBundle\Tests\Form\Types;


use AppBundle\Form\Types\IntArrayType;
use AppBundle\Form\Types\TextArrayType;
use AppBundle\Tests\ContainerAwareTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilder;

class StringToArrayTest extends ContainerAwareTestCase
{
    public function testTextToArrayTransformation() :void
    {
        $formBuilder = $this->getFormBuilder(new SampleClass());

        $form = $formBuilder->add('item', TextArrayType::class, ['label' => 'item'])
            ->getForm();

        $form->submit(['item' => '1,2,3,4,5']);

        /**
         * @var SampleClass $data
         */
        $data = $form->getData();
        $item = $data->getItem();
        static::assertIsArray($item);
        $expected = ['1','2','3','4','5'];
        static::assertEquals($expected, $item);
    }

    public function testTextToArrayTransformationRemovesEmptyString() :void
    {
        $formBuilder = $this->getFormBuilder(new SampleClass());

        $form = $formBuilder->add('item', TextArrayType::class, ['label' => 'item'])
            ->getForm();

        $form->submit(['item' => '1,2,3,,5']);

        /**
         * @var SampleClass $data
         */
        $data = $form->getData();
        $item = $data->getItem();
        static::assertIsArray($item);
        $expected = ['1','2','3','5'];
        static::assertEquals($expected, $item);
    }

    public function testTextToArrayTransformationLeavesArrayUntouched() :void
    {
        $formBuilder = $this->getFormBuilder(new SampleClass());

        $form = $formBuilder->add('item', TextArrayType::class, ['label' => 'item'])
            ->getForm();

        $form->submit(['item' => ['a', 'b', 'c']]);

        /**
         * @var SampleClass $data
         */
        $data = $form->getData();
        $item = $data->getItem();
        static::assertIsArray($item);
        $expected = ['a', 'b', 'c'];
        static::assertEquals($expected, $item);
    }

    public function testTextToArrayTransformationHandleEmpty() :void
    {
        $formBuilder = $this->getFormBuilder(new SampleClass());

        $form = $formBuilder->add('item', TextArrayType::class, ['label' => 'item'])
            ->getForm();

        $form->submit(['item' => '']);

        /**
         * @var SampleClass $data
         */
        $data = $form->getData();
        $item = $data->getItem();
        static::assertEquals(null, $item);
        $expected = null;
        static::assertEquals($expected, $item);
    }

    public function testIntToArrayTransformation() :void
    {
        $formBuilder = $this->getFormBuilder(new SampleClass());

        $form = $formBuilder->add('item', IntArrayType::class, ['label' => 'item'])
            ->getForm();

        $form->submit(['item' => '1,2,3,4,5']);

        /**
         * @var SampleClass $data
         */
        $data = $form->getData();
        $item = $data->getItem();
        static::assertIsArray($item);
        $expected = [1,2,3,4,5];
        static::assertEquals($expected, $item);

    }


}