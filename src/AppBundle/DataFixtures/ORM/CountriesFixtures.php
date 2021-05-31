<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CountriesFixtures extends Fixture
{

    public function load(ObjectManager $manager) :void
    {
        $countries = [
            ['id' => 'DE', 'name' => 'Germany'],
            ['id' => 'AT', 'name' => 'Austria'],
            ['id' => 'CH', 'name' => 'Switzerland'],
            ['id' => 'BE', 'name' => 'Belgium'],
            ['id' => 'NL', 'name' => 'Netherlands'],
            ['id' => 'FR', 'name' => 'France'],
            ['id' => 'DK', 'name' => 'Denmark'],
            ['id' => 'PL', 'name' => 'Poland'],
        ];

        foreach ($countries AS $spec) {
            $country = new Country();
            $country->setId($spec['id']);
            $country->setName($spec['name']);

            $manager->persist($country);
            $this->setReference('country-'.$spec['id'], $country);
        }

        $manager->flush();
    }
}