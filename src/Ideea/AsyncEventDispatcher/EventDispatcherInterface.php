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
 * Async event dispatcher.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface EventDispatcherInterface
{
    /**
     * Dispatch event
     *
     * @param string         $name
     * @param EventInterface $event
     *
     * @return bool
     */
    public function dispatch($name, EventInterface $event);
}
