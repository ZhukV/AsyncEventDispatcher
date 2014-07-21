<?php

/**
 * This file is part of the AsyncEventDispatcher package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\AsyncEventDispatcher\Adapter;

use Ideea\AsyncEventDispatcher\EventInterface;

/**
 * All providers should be implements of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface AdapterInterface
{
    /**
     * Send event to provider/storage
     *
     * @param string         $eventName
     * @param EventInterface $event
     *
     * @return bool
     */
    public function send($eventName, EventInterface $event);

    /**
     * Start receive process for this provider.
     * Attention: this method must be run as demon.
     *
     * @param string   $eventName
     * @param callable $callback
     * @param string   $receiverKey
     */
    public function receive($eventName, $callback, $receiverKey = null);
}
