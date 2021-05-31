<?php


namespace AppBundle\Tests\Lib\Entity;


use AppBundle\Entity\Product;
use AppBundle\Lib\Entity\Change;
use AppBundle\Lib\Entity\ChangeList;
use AppBundle\Lib\Entity\NotTrackedException;
use AppBundle\Tests\EntityManagerAwareTestCase;

class ChangeTest extends EntityManagerAwareTestCase
{
    /**
     * @throws \AppBundle\Lib\Entity\NotTrackedException
     */
    public function testChangeInitWorks()
    {
        /**
         * @var Product $product
         */
        $product = $this->getEntityManager()->getRepository(Product::class)->findOneBy([]);

        $clean = $product->getCleanData();

        static::assertIsArray($clean);
        static::assertNotCount(0, $clean);
        $changes = $product->getChanges();
        static::assertCount(0, $changes);
        self::assertFalse($product->isTracked('bogus'));
        self::assertTrue($product->isTracked('vat'));

        $vat = $product->getVat();
        $product->setVat(99.00);
        self::assertTrue($product->isChanged('vat'));
        static::assertEquals($vat, $product->getCleanValue('vat'));

        $changes = $product->getChanges();
        static::assertCount(1, $changes);

        try {
            $product->getChange('');
        } catch (\BadMethodCallException $b) {
            static::assertEquals('Key cannot be empty', $b->getMessage());
        }

        try {
            $product->getChange('bogus');
        } catch (NotTrackedException $nt) {
            static::assertEquals('Key: bogus is not tracked', $nt->getMessage());
        }

        try {
            $product->isChanged('bogus');
        } catch (NotTrackedException $nt) {
            static::assertEquals('Key: bogus is not tracked', $nt->getMessage());
        }

        try {
            $product->getCleanValue('bogus');
        } catch (NotTrackedException $nt) {
            static::assertEquals('Key: bogus is not tracked', $nt->getMessage());
        }
    }
}