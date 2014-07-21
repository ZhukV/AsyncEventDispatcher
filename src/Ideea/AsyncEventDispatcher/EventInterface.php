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
 * All events should be implement this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface EventInterface extends \Serializable
{
    /**
     * Set acknowledge
     *
     * @param bool $acknowledge
     *
     * @return Event
     */
    public function setAcknowledge($acknowledge);

    /**
     * Is event acknowledge
     *
     * @return bool
     */
    public function isAcknowledge();
}
