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

use Ideea\AsyncEventDispatcher\Adapter\AdapterInterface;

/**
 * Async event dispatcher
 */
class EventDispatcher implements EventDispatcherInterface
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
     * Send event to subscribers
     *
     * @param string         $name
     * @param EventInterface $event
     *
     * @return bool
     */
    public function dispatch($name, EventInterface $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        return $this->adapter->send($name, $event);
    }
}
