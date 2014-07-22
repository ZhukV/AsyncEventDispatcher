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
 * Abstract event
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Event implements EventInterface
{
    /**
     * @var bool
     */
    private $acknowledge = true;

    /**
     * {@inheritDoc}
     */
    public function setAcknowledge($acknowledge)
    {
        $this->acknowledge = (bool) $acknowledge;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isAcknowledge()
    {
        return $this->acknowledge;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        $data = array();

        foreach (get_object_vars($this) as $key => $value) {
            if ($key == 'acknowledge') {
                continue;
            }

            $data[$key] = $value;
        }

        return serialize($data);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
