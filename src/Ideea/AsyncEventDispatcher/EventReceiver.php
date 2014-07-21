<?php

/**
 * This file is part of the AsyncEventDispatcher package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\AsyncEventDispatcher;

use Ideea\AsyncEventDispatcher\Adapter\ProxyInterface;
use Ideea\AsyncEventDispatcher\Exception\Exception;
use Ideea\AsyncEventDispatcher\Adapter\AdapterInterface;

/**
 * Async event receiver
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class EventReceiver implements EventReceiverInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Construct
     *
     * @param AdapterInterface $provider
     */
    public function __construct(AdapterInterface $provider)
    {
        $this->adapter = $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function receive($eventName, $callback, $receiverKey = null)
    {
        if (!is_callable($callback)) {
            throw new Exception(sprintf(
                'The first parameters for receive event must be a callable, "%s" given.',
                gettype($callback)
            ));
        }

        $this->adapter->receive($eventName, $callback, $receiverKey);
    }

    /**
     * {@inheritDoc}
     */
    public function receiveProxy()
    {
        if (!$this->adapter instanceof ProxyInterface) {
            throw new Exception(sprintf(
                'Could not run proxy receiver for non proxy adapter "%s".',
                get_class($this->adapter)
            ));
        }

        // Run receive without arguments
        $this->adapter->receive(null, null, null);
    }
}
