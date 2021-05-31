<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Product;
use AppBundle\Entity\Item;
use AppBundle\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{

    public function load(ObjectManager $manager) :void
    {
        $apples = [
            [
                'name' => 'Boskop',
                'description' => 'Boskop, a classic',
                'countries' => [
                    ['country' => $this->getReference('country-DE'), 'price' => 100, 'vat' => 7.00, 'min' => 1, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 75, 'vat' => 7.00, 'min' => 3, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 50, 'vat' => 7.00, 'min' => 6, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 10, 'vat' => 7.00, 'min' => 12, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-AT'), 'price' => 100, 'vat' => 10.00, 'min' => 1, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-AT'), 'price' => 75, 'vat' => 10.00, 'min' => 3, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-AT'), 'price' => 50, 'vat' => 10.00, 'min' => 6, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-AT'), 'price' => 10, 'vat' => 10.00, 'min' => 12, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-CH'), 'price' => 75, 'vat' => 2.50, 'min' => 6, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-CH'), 'price' => 50, 'vat' => 2.50, 'min' => 12, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                ],
                "stock_up" => [
                    ["country" => $this->getReference('country-DE'), "quantity" => 100000, "start" => new \DateTime('2020-01-01'), "end" => new \DateTime("2025-01-01")],
                    ["country" => $this->getReference('country-AT'), "quantity" => 100000, "start" => new \DateTime('2020-01-01'), "end" => new \DateTime("2025-01-01")],
                    ["country" => $this->getReference('country-CH'), "quantity" => 10000, "start" => new \DateTime('2020-01-01'), "end" => new \DateTime("2025-01-01")],
                ]
            ],
            [
                'name' => 'golden_delicious',
                'description' => 'Another classic',
                'countries' => [
                    ['country' => $this->getReference('country-DE'), 'price' => 100, 'vat' => 7.00, 'min' => 1, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 75, 'vat' => 7.00, 'min' => 3, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 50, 'vat' => 7.00, 'min' => 6, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 10, 'vat' => 7.00, 'min' => 12, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-AT'), 'price' => 100, 'vat' => 10.00, 'min' => 1, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-AT'), 'price' => 75, 'vat' => 10.00, 'min' => 3, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-AT'), 'price' => 50, 'vat' => 10.00, 'min' => 6, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-AT'), 'price' => 10, 'vat' => 10.00, 'min' => 12, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-CH'), 'price' => 75, 'vat' => 2.50, 'min' => 6, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-CH'), 'price' => 50, 'vat' => 2.50, 'min' => 12, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                ],
                "stock_up" => [
                    ["country" => $this->getReference('country-DE'), "quantity" => 100000, "start" => new \DateTime('2020-01-01'), "end" => new \DateTime("2025-01-01")],
                    ["country" => $this->getReference('country-AT'), "quantity" => 100000, "start" => new \DateTime('2020-01-01'), "end" => new \DateTime("2025-01-01")],
                    ["country" => $this->getReference('country-CH'), "quantity" => 100000, "start" => new \DateTime('2020-01-01'), "end" => new \DateTime("2025-01-01")],
                ]
            ],
            [
                'name' => 'early_joe',
                'description' => 'Augustapfel',
                'countries' => [
                    ['country' => $this->getReference('country-DE'), 'price' => 100, 'vat' => 7.00, 'min' => 1, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 75, 'vat' => 7.00, 'min' => 3, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 50, 'vat' => 7.00, 'min' => 6, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                    ['country' => $this->getReference('country-DE'), 'price' => 10, 'vat' => 7.00, 'min' => 12, 'from' => '2020-01-01 00:00:00', 'until' => '2025-12-31 23:59:59'],
                ],
                "stock_up" => [
                    ["country" => $this->getReference('country-DE'), "quantity" => 100000, "start" => new \DateTime('2020-01-01'), "end" => new \DateTime("2025-01-01")],
                ]
            ],
        ];

        foreach ($apples AS $spec) {
            $item = new Item();
            $item->setName($spec['name']);
            $item->setDescription($spec['description']);

            $manager->persist($item);
            $manager->flush();


            foreach ($spec['countries'] AS $cspec) {
                $cp = new Product();
                $cp->setItem($item);
                $cp->setCountry($cspec['country']);
                $cp->setPrice($cspec['price']);
                $cp->setVat($cspec['vat']);
                $cp->setMinSubscriptionTime($cspec['min']);
                $cp->setAvailableFrom(new \DateTime($cspec['from']));
                $cp->setAvailableUntil(new \DateTime($cspec['until']));

                if ($item->getName() === 'early_joe') {
                    $refName = sprintf('product-%s-%s-%s', $item->getName(), $cp->getCountryCode(), $cp->getMinSubscriptionTime());
                    $this->setReference($refName, $cp);
                }

                $manager->persist($cp);
            }
            $manager->flush();
            if (!array_key_exists('stock_up', $spec)) {
                continue;
            }

            $this->stockUp($manager, $item, $spec['stock_up']);
        }
    }

    public function stockUp(ObjectManager $manager, Item $item, $settings) :void
    {
        $repo = $manager->getRepository(Stock::class);

        foreach ($settings AS $spec) {
            $country = $spec['country'];
            $quantity = $spec['quantity'];
            $start = $spec['start'];
            $end = $spec['end'];

            $repo->stockUp($item->getId(), $country->getId(), $quantity, $start, $end);
        }



    }

    public function getDependencies() :array
    {
        return [
            CountriesFixtures::class
        ];
    }
}