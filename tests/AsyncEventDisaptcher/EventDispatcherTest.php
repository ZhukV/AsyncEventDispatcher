<?php

namespace Ideea\AsyncEventDispatcher;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Ideea\AsyncEventDispatcher\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->adapter = $this->getMockForAbstractClass(
            'Ideea\AsyncEventDispatcher\Adapter\AdapterInterface'
        );

        $this->eventDispatcher = new EventDispatcher($this->adapter);
    }

    /**
     * Test send event
     */
    public function testDispatch()
    {
        $event = new Event();

        $this->adapter->expects($this->once())->method('send')
            ->with('foo-bar-testing', $event);

        $this->eventDispatcher->dispatch('foo-bar-testing', $event);
    }

    /**
     * Test send event with event is empty
     */
    public function testDispatchWithEventIsEmpty()
    {
        $this->adapter->expects($this->once())->method('send')
            ->with('bar-foo-testing', $this->isInstanceOf('Ideea\AsyncEventDispatcher\Event'));

        $this->eventDispatcher->dispatch('bar-foo-testing');
    }
}