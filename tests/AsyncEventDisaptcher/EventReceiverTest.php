<?php

namespace Ideea\AsyncEventDispatcher;

class EventReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Ideea\AsyncEventDispatcher\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    /**
     * @var EventReceiver
     */
    private $eventReceiver;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->adapter = $this->getMockForAbstractClass(
            'Ideea\AsyncEventDispatcher\Adapter\AdapterInterface'
        );

        $this->eventReceiver = new EventReceiver($this->adapter);
    }

    /**
     * Test receive
     */
    public function testReceive()
    {
        $callback = function (){};
        $eventName = 'foo-bar-testing';
        $key = 'key1';

        $this->adapter->expects($this->once())->method('receive')
            ->with($eventName, $callback, $key);

        $this->eventReceiver->receive($eventName, $callback, $key);
    }

    /**
     * Test receive with key is empty
     */
    public function testReceiveWithKeyIsEmpty()
    {
        $callback = function (){};
        $eventName = 'bar-foo-testing';

        $this->adapter->expects($this->once())->method('receive')
            ->with($eventName, $callback, null);

        $this->eventReceiver->receive($eventName, $callback);
    }

    /**
     * Test receive with invalid callback
     *
     * @expectedException \Ideea\AsyncEventDispatcher\Exception\Exception
     * @expectedExceptionMessage The first parameters for receive event must be a callable, "string" given.
     */
    public function testReceiveWithInvalidCallback()
    {
        $callback = 'invalid';
        $eventName = 'foo-bar';

        $this->adapter->expects($this->never())->method('receive');

        $this->eventReceiver->receive($eventName, $callback);
    }
}