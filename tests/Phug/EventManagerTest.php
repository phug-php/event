<?php

namespace Phug\Test;

use Phug\EventInterface;

/**
 * @coversDefaultClass \Phug\EventManagerTrait
 */
class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::attach
     * @covers ::trigger
     */
    public function testAttach()
    {

        require_once __DIR__.'/MockEventManager.php';

        $called = false;
        $mgr = new MockEventManager;
        $mgr->attach('test.event', function (EventInterface $e) use (&$called) {

            $called = true;
        });
        $mgr->trigger('test.event');

        self::assertTrue($called, 'event listener was called');
    }

    /**
     * @covers ::attach
     * @covers ::trigger
     */
    public function testPriorityAttachInOrder()
    {

        require_once __DIR__.'/MockEventManager.php';

        $order = [];
        $mgr = new MockEventManager;
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 1;
        }, 10);
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 2;
        }, 20);
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 3;
        }, 30);
        $mgr->trigger('test.event');

        self::assertSame([1, 2, 3], $order, 'event listeners were called in order');
    }

    /**
     * @covers ::attach
     * @covers ::trigger
     */
    public function testPriorityAttachInReversedOrder()
    {

        require_once __DIR__.'/MockEventManager.php';

        $order = [];
        $mgr = new MockEventManager;
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 3;
        }, 30);
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 2;
        }, 20);
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 1;
        }, 10);
        $mgr->trigger('test.event');

        self::assertSame([1, 2, 3], $order, 'event listeners were called in order');
    }

    /**
     * @covers ::attach
     * @covers ::trigger
     */
    public function testPriorityAttachInMixedOrder()
    {

        require_once __DIR__.'/MockEventManager.php';

        $order = [];
        $mgr = new MockEventManager;
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 3;
        }, 30);
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 1;
        }, 10);
        $mgr->attach('test.event', function (EventInterface $e) use (&$order) {

            $order[] = 2;
        }, 20);
        $mgr->trigger('test.event');

        self::assertSame([1, 2, 3], $order, 'event listeners were called in order');
    }

    /**
     * @covers ::attach
     * @covers ::detach
     * @covers ::trigger
     */
    public function testDetach()
    {

        require_once __DIR__.'/MockEventManager.php';

        $mgr = new MockEventManager;

        $called = false;
        $listener = function (EventInterface $e) use (&$called) {

            $called = true;
        };
        $mgr->attach('test.event', $listener);
        $mgr->trigger('test.event');
        self::assertTrue($called, 'event listener was called');

        $called = false;
        $mgr->detach('test.event', $listener);
        $mgr->trigger('test.event');
        self::assertFalse($called, 'event listener was not called after detach');
    }

    /**
     * @covers ::attach
     * @covers ::detach
     * @covers ::trigger
     */
    public function testIfDetachTouchesOtherEvents()
    {

        require_once __DIR__.'/MockEventManager.php';

        $mgr = new MockEventManager;

        $called = false;
        $listener = function (EventInterface $e) use (&$called) {

            $called = true;
        };
        $mgr->attach('test.event', $listener);
        $mgr->trigger('test.event');
        self::assertTrue($called, 'event listener was called');

        $called = false;
        $mgr->detach('other.event', $listener);
        $mgr->trigger('test.event');
        self::assertTrue($called, 'event listener was called again after detach');
    }

    /**
     * @covers ::attach
     * @covers ::clearListeners
     * @covers ::trigger
     */
    public function testClearListeners()
    {

        require_once __DIR__.'/MockEventManager.php';

        $calls = 0;
        $mgr = new MockEventManager;
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });
        $mgr->trigger('test.event');

        self::assertSame(3, $calls, '3 different listeners have been called');

        $calls = 0;
        $mgr->clearListeners('test.event');
        $mgr->trigger('test.event');

        self::assertSame(0, $calls, '0 listeners have been called after clearListeners');
    }

    /**
     * @covers ::attach
     * @covers ::clearListeners
     * @covers ::trigger
     */
    public function testIfClearListenersTouchesOtherEvents()
    {

        require_once __DIR__.'/MockEventManager.php';

        $calls = 0;
        $mgr = new MockEventManager;
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });
        $mgr->trigger('test.event');

        self::assertSame(3, $calls, '3 different listeners have been called');

        $calls = 0;
        $mgr->clearListeners('other.event');
        $mgr->trigger('test.event');

        self::assertSame(3, $calls, '3 listeners have been called again after clearListeners');
    }

    /**
     * @covers ::attach
     * @covers ::trigger
     */
    public function testMultipleTriggerCallsOnSameEvent()
    {

        require_once __DIR__.'/MockEventManager.php';

        $mgr = new MockEventManager;

        $called = false;
        $listener = function (EventInterface $e) use (&$called) {

            $called = true;
        };
        $mgr->attach('test.event', $listener);
        $mgr->trigger('test.event');
        self::assertTrue($called, 'event listener was called');

        $called = false;
        $mgr->trigger('test.event');
        self::assertTrue($called, 'event listener was called a second time');

        $called = false;
        $mgr->trigger('test.event');
        self::assertTrue($called, 'event listener was called a third time');
    }

    /**
     * @covers ::attach
     * @covers ::trigger
     */
    public function testMultipleTriggerCallsOnSameEventWithMultipleListeners()
    {

        require_once __DIR__.'/MockEventManager.php';

        $calls = 0;
        $mgr = new MockEventManager;
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });
        $mgr->attach('test.event', function (EventInterface $e) use (&$calls) {

            $calls++;
        });

        $mgr->trigger('test.event');
        self::assertSame(3, $calls, '3 different listeners have been called');

        $calls = 0;
        $mgr->trigger('test.event');
        self::assertSame(3, $calls, '3 different listeners have been called a second time');

        $calls = 0;
        $mgr->trigger('test.event');
        self::assertSame(3, $calls, '3 different listeners have been called a third time');
    }

    /**
     * @covers ::attach
     * @covers ::trigger
     */
    public function testDifferentEventListenersWithReturnValue()
    {

        require_once __DIR__.'/MockEventManager.php';

        $mgr = new MockEventManager;
        $mgr->attach('test.first_event', function (EventInterface $e) {

            return 'first_event';
        });
        $mgr->attach('test.second_event', function (EventInterface $e) {

            return 'second_event';
        });
        $mgr->attach('test.third_event', function (EventInterface $e) {

            return 'third_event';
        });

        self::assertSame('first_event', $mgr->trigger('test.first_event'), 'first event return value');
        self::assertSame('second_event', $mgr->trigger('test.second_event'), 'second event return value');
        self::assertSame('third_event', $mgr->trigger('test.third_event'), 'third event return value');
    }

    /**
     * @covers ::attach
     * @covers ::trigger
     * @covers \Phug\Event::stopPropagation
     */
    public function testPropagationStop()
    {

        require_once __DIR__.'/MockEventManager.php';

        $mgr = new MockEventManager;
        $mgr->attach('test.event', function (EventInterface $e) {

            return 'first';
        });
        $mgr->attach('test.event', function (EventInterface $e) {

            $e->stopPropagation(true);

            return 'second';
        });
        $mgr->attach('test.event', function (EventInterface $e) {

            return 'third';
        });

        self::assertSame('second', $mgr->trigger('test.event'), 'event return value');
    }
}
