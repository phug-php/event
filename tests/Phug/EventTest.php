<?php

namespace Phug\Test;

use Phug\Event;

/**
 * @coversDefaultClass \Phug\Event
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getName
     * @covers ::setName
     */
    public function testGetSetName()
    {
        $e = new Event('test.event');

        self::assertSame('test.event', $e->getName(), 'get name');

        $e->setName('other.event');

        self::assertSame('other.event', $e->getName(), 'get name after set');
    }

    /**
     * @covers ::__construct
     * @covers ::getTarget
     * @covers ::setTarget
     */
    public function testGetSetTarget()
    {
        $e = new Event('test.event');
        self::assertNull($e->getTarget(), 'target is null when not passed');

        $target = new \stdClass();
        $e = new Event('test.event', $target);

        self::assertSame($target, $e->getTarget(), 'get target');

        $otherTarget = new \stdClass();
        $e->setTarget($otherTarget);

        self::assertSame($otherTarget, $e->getTarget(), 'get target after set');
    }

    /**
     * @covers ::__construct
     * @covers ::getParams
     * @covers ::getParam
     * @covers ::setParams
     */
    public function testGetSetParams()
    {
        $e = new Event('test.event');
        self::assertInternalType('array', $e->getParams());
        self::assertCount(0, $e->getParams(), 'params are empty when not passed');

        $params = ['test_key' => 'test value'];
        $e = new Event('test.event', null, $params);

        self::assertSame(['test_key' => 'test value'], $e->getParams(), 'get params');
        self::assertSame('test value', $e->getParam('test_key'), 'get single param');

        $otherParams = ['other_test_key' => 'other test value'];
        $e->setParams($otherParams);

        self::assertSame(['other_test_key' => 'other test value'], $e->getParams(), 'get params after set');
        self::assertSame('other test value', $e->getParam('other_test_key'), 'get single param after set');
    }

    /**
     * @covers ::__construct
     * @covers ::stopPropagation
     * @covers ::isPropagationStopped
     */
    public function testStopPropagation()
    {
        $e = new Event('test.event');

        self::assertFalse($e->isPropagationStopped(), 'propagation isnt stopped after construct');

        $e->stopPropagation(true);

        self::assertTrue($e->isPropagationStopped(), 'propagation is stopped after set');

        $e->stopPropagation(false);

        self::assertFalse($e->isPropagationStopped(), 'propagation isnt stopped after second set');
    }
}
