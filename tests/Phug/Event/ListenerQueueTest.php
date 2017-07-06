<?php

namespace Phug\Test\Event;

use Phug\Event;

/**
 * @coversDefaultClass \Phug\Event\ListenerQueue
 */
class ListenerQueueTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::compare
     */
    public function testCompare()
    {

        $queue = new Event\ListenerQueue();

        self::assertSame(0, $queue->compare(5, 5), '0 if same value');
        self::assertSame(1, $queue->compare(3, 5), '1 if smaller');
        self::assertSame(-1, $queue->compare(7, 5), '-1 if greater');
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {

        $queue = new Event\ListenerQueue();

        self::assertCount(0, $queue, 'count is 0 after construct');
        $queue->insert(function () {
        }, 10);
        self::assertCount(1, $queue, 'count is 1 after first insert');
        $queue->insert(function () {
        }, 10);
        self::assertCount(2, $queue, 'count is 2 after second insert');
    }

    public function provideParameterValues()
    {

        return [
            [null],
            [false],
            [true],
            [24],
            [2.4],
            ['test'],
            [new \stdClass()],
            [[1, 2, 3]]
        ];
    }

    /**
     * @covers ::insert
     * @dataProvider provideParameterValues
     */
    public function testInsertWithNonCallback($parameterValue)
    {

        if (method_exists(self::class, 'expectException')) {
            self::expectException(\InvalidArgumentException::class);
        } else {
            self::setExpectedException(\InvalidArgumentException::class);
        }

        $queue = new Event\ListenerQueue();
        $queue->insert('test', $parameterValue);
    }
}
