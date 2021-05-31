<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Customer;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\Store;
use AppBundle\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{

    /**
     * @param ObjectManager $manager
     * @return bool
     * @throws \Exception
     */
    public function load(ObjectManager $manager) :void
    {
        
        $customers = [
            [
                'name' => 'customer_1',
                'stores' => [
                    [
                        'name' => 'customer_1_store_1',
                        'country' => $this->getReference('country-DE'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                        'subscriptions' => [
                            [
                                'product' => $this->getReference('product-early_joe-DE-12'),
                                'quantity' => 10000,
                                'start' => '2020-01-01 00:00:00',
                                'end' => '2025-01-31 23:59:59'
                            ],
                            [
                                'product' => $this->getReference('product-early_joe-DE-1'),
                                'quantity' => 10000,
                                'start' => '2021-01-01 00:00:00',
                                'end' => '2021-01-31 23:59:59'
                            ],
                            [
                                'product' => $this->getReference('product-early_joe-DE-12'),
                                'quantity' => 10000,
                                'start' => '2015-01-01 00:00:00',
                                'end' => '2016-01-31 23:59:59'
                            ],
                        ]
                    ],
                    [
                        'name' => 'customer_1_store_2',
                        'country' => $this->getReference('country-DE'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                        'subscriptions' => [
                            [
                                'product' => $this->getReference('product-early_joe-DE-12'),
                                'quantity' => 10000,
                                'start' => '2020-01-01 00:00:00',
                                'end' => '2025-01-31 23:59:59'
                            ],
                            [
                                'product' => $this->getReference('product-early_joe-DE-1'),
                                'quantity' => 10000,
                                'start' => '2021-01-01 00:00:00',
                                'end' => '2021-01-31 23:59:59'
                            ],
                            [
                                'product' => $this->getReference('product-early_joe-DE-12'),
                                'quantity' => 10000,
                                'start' => '2015-01-01 00:00:00',
                                'end' => '2016-01-31 23:59:59'
                            ],
                        ]
                    ],
                    [
                        'name' => 'customer_1_store_2',
                        'country' => $this->getReference('country-AT'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                ]
            ],
            [
                'name' => 'customer_2',
                'stores' => [
                    [
                        'name' => 'customer_2_store_1',
                        'country' => $this->getReference('country-DE'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                    [
                        'name' => 'customer_2_store_2',
                        'country' => $this->getReference('country-DE'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                ]
            ],
            [
                'name' => 'customer_3',
                'stores' => [
                    [
                        'name' => 'customer_3_store_1',
                        'country' => $this->getReference('country-CH'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                    [
                        'name' => 'customer_3_store_2',
                        'country' => $this->getReference('country-NL'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                    [
                        'name' => 'customer_3_store_2',
                        'country' => $this->getReference('country-AT'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                ]
            ],
            [
                'name' => 'customer_4',
                'stores' => [
                    [
                        'name' => 'customer_4_store_2',
                        'country' => $this->getReference('country-AT'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                ]
            ],
            [
                'name' => 'customer_5',
                'stores' => [
                    [
                        'name' => 'customer_5_store_1',
                        'country' => $this->getReference('country-DE'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                    [
                        'name' => 'customer_5_store_2',
                        'country' => $this->getReference('country-DE'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                    [
                        'name' => 'customer_5_store_2',
                        'country' => $this->getReference('country-AT'),
                        'description' => '',
                        'start' => '2015-01-01 00:00:00',
                    ],
                ]
            ],
        ];

        foreach ($customers AS $spec) {
            $customer = new Customer();
            $customer->setName($spec['name']);
            $manager->persist($customer);
            $manager->flush();

            foreach ($spec['stores'] AS $store_spec) {
                $store = new Store();
                $store->setName($store_spec['name']);
                $store->setCustomer($customer);
                $store->setCountry($store_spec['country']);
                $store->setStartDate(new \DateTime($store_spec['start']));

                $manager->persist($store);
                $manager->flush();

                if (array_key_exists('subscriptions', $store_spec) && is_array($store_spec['subscriptions'])) {
                    $subscriptions = [];
                    foreach ($store_spec['subscriptions'] AS $sub_spec) {
                        $sub = new Subscription();
                        $sub->setStore($store);
                        $sub->setProduct($sub_spec['product']);
                        $sub->setQuantity($sub_spec['quantity']);
                        $sub->setStartDate(new \DateTime($sub_spec['start']));
                        $sub->setEndDate(new \DateTime($sub_spec['end']));

                        $manager->persist($sub);

                        $subscriptions[] = $sub;
                    }
                    $manager->flush();

                    foreach ($subscriptions AS $subscription) {
                        $manager->getRepository(Shipment::class)->createShipments($subscription);
                    }
                }
            }

        }
    }

    public function getDependencies() :array
    {
        return [
            ProductFixtures::class
        ];
    }
}