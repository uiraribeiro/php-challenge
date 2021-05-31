<?php


namespace AppBundle\Tests;


use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpKernel\KernelInterface;

class ContainerAwareTestCase extends KernelTestCase
{
    public function getContainer() :ContainerInterface
    {
        if (!static::$kernel instanceof KernelInterface) {
            static::bootKernel();
        }

        $container = static::$kernel->getContainer();

        if (null === $container) {
            static::bootKernel();

            return static::$kernel->getContainer();
        }

        return $container;
    }

    /**
     * @param string $form
     * @param $data
     * @param array $options
     * @return Form
     */
    protected function createForm(string $form, $data, array $options = []) :Form
    {
        return $this->getContainer()->get('form.factory')->create($form, $data, $options);
    }

    /**
     * @param $data
     * @param array $options
     * @return FormBuilder
     */
    protected function getFormBuilder($data, array $options = []) :FormBuilder
    {
        return $this->getContainer()->get('form.factory')->createBuilder(FormType::class, $data, $options);
    }

    public function getSerializer() :Serializer
    {
        return $this->getContainer()->get('serializer');
    }
}