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

/**
 * Async event receiver
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface EventReceiverInterface
{
    /**
     * Run receiver process for event name
     *
     * @param string   $eventName
     * @param callable $callback
     * @param string   $receiverKey
     *
     * @throws Exception\Exception
     */
    public function receive($eventName, $callback, $receiverKey = null);

    /**
     * Run proxy receiver
     *
     * @throws Exception\Exception
     */
    public function receiveProxy();
}
