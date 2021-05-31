<?php


namespace AppBundle\Tests;


use AppBundle\DataFixtures\ORM\CountriesFixtures;
use AppBundle\DataFixtures\ORM\CustomerFixtures;
use AppBundle\DataFixtures\ORM\ProductFixtures;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Item;
use AppBundle\Entity\Store;
use AppBundle\Entity\Country;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityManagerAwareTestCase extends ContainerAwareTestCase
{

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public static function bootstrapDatabase($loadFixtures = true): void
    {
        $kernel = static::createKernel();
        $kernel->boot();

        if ('test' !== $kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the test environment');
        }

        $container = $kernel->getContainer();
        /**
         * @var EntityManager $em
         */
        $em = $container->get('doctrine')->getManager();
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        if ($loadFixtures) {
            $loader = static::getFixtureLoader($container);
            static::getDefaultFixtures($loader);
            static::loadFixtures($loader, $container);
        }

        $kernel->shutdown();
    }

    public static function getFixtureLoader(ContainerInterface $container) :ContainerAwareLoader
    {
        return new ContainerAwareLoader($container);
    }

    public static function loadFixtures(ContainerAwareLoader $loader, ContainerInterface $container) :bool
    {
        $entityManager = $container->get('doctrine')->getManager();
        $executor = new ORMExecutor($entityManager, new ORMPurger($entityManager));
        $executor->execute($loader->getFixtures());

        return true;
    }

    public static function getDefaultFixtures(ContainerAwareLoader $loader) :ContainerAwareLoader
    {
        $loader->addFixture(new CountriesFixtures());
        $loader->addFixture(new ProductFixtures());
        $loader->addFixture(new CustomerFixtures());

        return $loader;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager() :EntityManager
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    public static function assertQueryBuilderParametersMatch(QueryBuilder $qb, array $match, array $skip = []) :void
    {
        foreach ($match AS $k => $v) {
            if (in_array($k, $skip, true)) {
                continue;
            }

            $parameter = $qb->getParameter($k);
            static::assertInstanceOf(Parameter::class, $parameter);
            static::assertEquals($k, $parameter->getName());

            if ($k === 'search') {
                $v = '%'.$v.'%';
            }

            static::assertEquals($v, $parameter->getValue());
        }
    }

    /**
     * Tries to find a customer and returns it
     * @param string $name
     * @return Customer|null
     */
    protected function getCustomerByName(string $name) :?Customer
    {
        return $this->getEntityManager()->getRepository(Customer::class)->findOneBy(['name' => $name]);
    }

    /**
     * Tries to find a store and returns it
     * @param string $name
     * @return Store|null
     */
    protected function getStoreByName(string $name) :?Store
    {
        return $this->getEntityManager()->getRepository(Store::class)->findOneBy(['name' => $name]);
    }

    protected function getCountryByName(string $name):?Country
    {
        return $this->getEntityManager()->getRepository(Country::class)->find($name);
    }

    protected function getItemByName(string $name):?Item
    {
        return $this->getEntityManager()->getRepository(Item::class)->findOneBy(['name' => $name]);
    }

    protected function getProductsForItem(string $item, bool $activeOnly = true, ?string $country = null, ?int $runtime = null) :array
    {
        $item = $this->getItemByName($item);

        return $item->getProductsBy($country,$runtime, $activeOnly);
    }
}