<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Shipment;
use AppBundle\Entity\Subscription;

/**
 * ShipmentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShipmentRepository extends \Doctrine\ORM\EntityRepository
{
    public function createShipments(Subscription $subscription) :void
    {
        $sql = "REPLACE INTO shipments (subscription_id, product_id, item_id, country_id, quantity, state, shipping_date) "
            ." VALUES (:subscription_id, :product_id, :item_id, :country_id, :quantity, :state, :shipping_date)";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $start = clone $subscription->getStartDate();
        $end = clone $subscription->getEndDate();
        $params = [
            'subscription_id' => $subscription->getId(),
            'product_id' => $subscription->getProduct()->getId(),
            'item_id' => $subscription->getItemId(),
            'country_id' => $subscription->getCountryCode(),
            'quantity' => $subscription->getQuantity(),
            'state' => Shipment::SHIPMENT_SHIPPED,
        ];

        $interval = new \DateInterval('P1M');
        $now = new \DateTime();
        $now = (int) $now->getTimestamp();

        while($start < $end) {
            $params['shipping_date'] = $start->format('Y-m-d H:i:s');
            if ($start->getTimestamp() > $now) {
                $params['state'] = Shipment::SHIPMENT_PENDING;
            }

            $stmt->execute($params);
            $start->add($interval);
        }


    }
}
