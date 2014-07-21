<?php

/**
 * This file is part of the AsyncEventDispatcher package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\AsyncEventDispatcher\Adapter\Amqp;

use Ideea\AsyncEventDispatcher\Event;
use Ideea\AsyncEventDispatcher\EventInterface;
use Ideea\AsyncEventDispatcher\Adapter\AdapterInterface;

/**
 * Amqp event provider
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class AmqpAdapter implements AdapterInterface
{
    /**
     * @var \AMQPConnection
     */
    private $connection;

    /**
     * @var \AMQPChannel
     */
    private $channel;

    /**
     * @var \AMQPExchange
     */
    private $exchange;

    /**
     * @var string
     */
    private $exchangeName = 'async_event_dispatcher';

    /**
     * @var string
     */
    private $exchangeType = AMQP_EX_TYPE_DIRECT;

    /**
     * @var int
     */
    private $exchangeFlags = AMQP_DURABLE;

    /**
     * @var integer
     */
    private $consumeFlags = AMQP_NOPARAM;

    /**
     * @var string
     */
    private $queuePrefix = 'aed';

    /**
     * Construct
     *
     * @param \AMQPChannel|\AMQPConnection $channel
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($channel)
    {
        if ($channel instanceof \AMQPChannel) {
            $this->channel = $channel;
        } elseif ($channel instanceof \AMQPConnection) {
            $this->connection = $channel;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The first argument must be instance of "AMQPChannel" or "AMQPConnection", "%s" given.',
                is_object($channel) ? get_class($channel) : gettype($channel)
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function send($eventName, EventInterface $event)
    {
        return $this->getExchange()->publish(serialize($event), $this->generateRoutingKey($eventName));
    }

    /**
     * {@inheritDoc}
     */
    public function receive($eventName, $callback, $receiverKey = null)
    {
        $queue = $this->getQueue($eventName, $receiverKey);

        $queue->consume(function (\AMQPEnvelope $envelope) use ($callback, $queue) {
            $message = $envelope->getBody();

            /** @var \Ideea\AsyncEventDispatcher\Event $event */
            $event = unserialize($message);

            if (!$event instanceof Event) {
                throw new \RuntimeException(sprintf(
                    'Invalid event object. Must be implement of "Event" instance, "%s" given.',
                    is_object($event) ? get_class($event) : gettype($event)
                ));
            }

            call_user_func($callback, $event);

            if ($event->isAcknowledge()) {
                $queue->ack($envelope->getDeliveryTag());
            } else {
                $queue->nack($envelope->getDeliveryTag());
            }
        }, $this->consumeFlags);
    }

    /**
     * Set exchange name
     *
     * @param string $exchangeName
     *
     * @return AmqpAdapter
     */
    public function setExchangeName($exchangeName)
    {
        $this->exchangeName = $exchangeName;

        return $this;
    }

    /**
     * Get exchange name
     *
     * @return string
     */
    public function getExchangeName()
    {
        return $this->exchangeName;
    }

    /**
     * Set exchange type
     *
     * @param string $exchangeType
     *
     * @return AmqpAdapter
     *
     * @throws \InvalidArgumentException
     */
    public function setExchangeType($exchangeType)
    {
        if (!in_array($exchangeType, array(AMQP_EX_TYPE_DIRECT, AMQP_EX_TYPE_TOPIC))) {
            throw new \InvalidArgumentException(sprintf(
                'The AMQP exchange type must be "direct" or "topic", "%s" given.',
                $exchangeType
            ));
        }

        $this->exchangeType = $exchangeType;

        return $this;
    }

    /**
     * Get exchange type
     *
     * @return string
     */
    public function getExchangeType()
    {
        return $this->exchangeType;
    }

    /**
     * Set exchange flags
     *
     * @param int $exchangeFlags
     *
     * @return AmqpAdapter
     */
    public function setExchangeFlags($exchangeFlags)
    {
        $this->exchangeFlags = $exchangeFlags;

        return $this;
    }

    /**
     * Get exchange flags
     *
     * @return int
     */
    public function getExchangeFlags()
    {
        return $this->exchangeFlags;
    }

    /**
     * Set queue consume flags
     *
     * @param int $flags
     *
     * @return AmqpAdapter
     */
    public function setConsumeFlags($flags)
    {
        $this->consumeFlags = $flags;

        return $this;
    }

    /**
     * Get queue consume flags
     *
     * @return int
     */
    public function getConsumeFlags()
    {
        return $this->consumeFlags;
    }

    /**
     * Set queue prefix
     *
     * @param string $prefix
     *
     * @return AmqpAdapter
     */
    public function setQueuePrefix($prefix)
    {
        $this->queuePrefix = $prefix;

        return $this;
    }

    /**
     * Get queue prefix
     *
     * @return string
     */
    public function getQueuePrefix()
    {
        return $this->queuePrefix;
    }

    /**
     * Get exchange
     *
     * @return \AMQPExchange
     */
    private function getExchange()
    {
        if ($this->exchange) {
            return $this->exchange;
        }

        $exchange = new \AMQPExchange($this->getChannel());
        $exchange->setName($this->exchangeName);
        $exchange->setType($this->exchangeType);
        $exchange->setFlags($this->exchangeFlags);
        $exchange->declareExchange();

        return $this->exchange = $exchange;
    }

    /**
     * Get queue for receiving
     *
     * @param string $eventName
     * @param string $receiverKey
     *
     * @return \AMQPQueue
     */
    private function getQueue($eventName, $receiverKey = null)
    {
        if (!$receiverKey) {
            $receiverKey = uniqid();
            $temporaryQueue = true;
        } else {
            $temporaryQueue = false;
        }

        $queue = new \AMQPQueue($this->getChannel());
        $queue->setName($this->queuePrefix. '.' . $eventName . '.' . $receiverKey);

        if (!$temporaryQueue) {
            $queue->setFlags(AMQP_DURABLE);
        }

        $queue->declareQueue();
        $queue->bind($this->getExchange()->getName(), $this->generateRoutingKey($eventName));

        return $queue;
    }

    /**
     * Get channel
     *
     * @return \AMQPChannel
     */
    private function getChannel()
    {
        if ($this->channel) {
            return $this->channel;
        }

        if (!$this->connection->isConnected()) {
            $this->connection->connect();
        }

        return $this->channel = new \AMQPChannel($this->connection);
    }

    /**
     * Generate routing key
     *
     * @param string $eventName
     *
     * @return string
     */
    private function generateRoutingKey($eventName)
    {
        return $this->queuePrefix . '.rk.' . $eventName;
    }
}
