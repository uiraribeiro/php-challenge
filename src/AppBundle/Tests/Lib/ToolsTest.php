<?php


namespace AppBundle\Tests\Lib;

use AppBundle\Lib\Tools;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ToolsTest extends KernelTestCase
{
    public function testGetFirstDayOfMonth() :void
    {
        $date = new \DateTime('2021-01-23');
        $first = Tools::getFirstDayDateTimeForMonth($date);
        static::assertEquals('2021-01-01 00:00:00', $first->format('Y-m-d H:i:s'));
    }

    public function testGetLastDayOfMonth() :void
    {
        $date = new \DateTime('2021-01-23');
        $first = Tools::getLastDayDateTimeForMonth($date);
        static::assertEquals('2021-01-31 23:59:59', $first->format('Y-m-d H:i:s'));
    }

    public function testGetFirstAndLastDayOfMonth() :void
    {
        $date = new \DateTime('2021-01-23');
        $firstLast = Tools::getFirstAndLastDayOfMonth($date);
        static::assertIsArray($firstLast);
        static::assertArrayHasKey('start_date', $firstLast);
        static::assertArrayHasKey('end_date', $firstLast);
        static::assertInstanceOf(\DateTime::class, $firstLast['start_date']);
        static::assertInstanceOf(\DateTime::class, $firstLast['end_date']);
        static::assertEquals('2021-01-01 00:00:00', $firstLast['start_date']->format('Y-m-d H:i:s'));
        static::assertEquals('2021-01-31 23:59:59', $firstLast['end_date']->format('Y-m-d H:i:s'));

        $firstLast = Tools::getFirstAndLastDayOfMonth($date, 'Y-m-d H:i:s');
        static::assertIsArray($firstLast);
        static::assertArrayHasKey('start_date', $firstLast);
        static::assertArrayHasKey('end_date', $firstLast);
        static::assertIsString($firstLast['start_date']);
        static::assertIsString($firstLast['end_date']);
        static::assertEquals('2021-01-01 00:00:00', $firstLast['start_date']);
        static::assertEquals('2021-01-31 23:59:59', $firstLast['end_date']);
    }
}