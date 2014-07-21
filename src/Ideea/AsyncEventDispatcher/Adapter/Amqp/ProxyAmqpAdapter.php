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

use Ideea\AsyncEventDispatcher\Adapter\ProxyInterface;
use Ideea\AsyncEventDispatcher\EventInterface;
use Ideea\AsyncEventDispatcher\Adapter\AdapterInterface;

/**
 * Proxy AMQP adapter
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ProxyAmqpAdapter implements AdapterInterface, ProxyInterface
{
    /**
     * @var AdapterInterface $provider
     */
    private $adapter;

    /**
     * @var \AMQPChannel
     */
    private $channel;

    /**
     * @var \AMQPConnection
     */
    private $connection;

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
     * @var string
     */
    private $queueName = 'aed.proxy_events';

    /**
     * @var string
     */
    private $routingKey = 'aed.rk_proxy_events';

    /**
     * Construct
     *
     * @param AdapterInterface             $adapter
     * @param \AMQPConnection|\AMQPChannel $channel
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(AdapterInterface $adapter, $channel)
    {
        $this->adapter = $adapter;

        if ($channel instanceof \AMQPChannel) {
            $this->channel = $channel;
        } elseif ($channel instanceof \AMQPConnection) {
            $this->connection = $channel;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The second argument must be instance of "AMQPChannel" or "AMQPConnection", "%s" given.',
                is_object($channel) ? get_class($channel) : gettype($channel)
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function send($eventName, EventInterface $event)
    {
        $this->getExchange()->publish(serialize(array(
            $eventName,
            $event
        )), $this->routingKey);
    }

    /**
     * {@inheritDoc}
     */
    public function receive($eventName, $callback, $receiverKey = null)
    {
        $queue = $this->getQueue();

        $queue->consume(function (\AMQPEnvelope $envelope) use ($queue) {
            list ($eventName, $event) = unserialize($envelope->getBody());

            $this->adapter->send($eventName, $event);

            $queue->ack($envelope->getDeliveryTag());
        }, AMQP_NOPARAM);
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
     * Set queue name
     *
     * @param string $name
     *
     * @return ProxyAmqpAdapter
     */
    public function setQueueName($name)
    {
        $this->queueName = $name;

        return $this;
    }

    /**
     * Get queue name
     *
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * Set routing key
     *
     * @param string $routingKey
     *
     * @return ProxyAmqpAdapter
     */
    public function setRoutingKey($routingKey)
    {
        $this->routingKey = $routingKey;

        return $this;
    }

    /**
     * Get routing key
     *
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * Get queue
     *
     * @return \AMQPQueue
     */
    private function getQueue()
    {
        $queue = new \AMQPQueue($this->getChannel());
        $queue->setName($this->queueName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queue->bind($this->getExchange()->getName(), $this->routingKey);

        return $queue;
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
}
