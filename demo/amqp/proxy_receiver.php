#!/usr/bin/env php
<?php

use Ideea\AsyncEventDispatcher\Adapter\Amqp\AmqpAdapter;
use Ideea\AsyncEventDispatcher\Adapter\Amqp\ProxyAmqpAdapter;
use Ideea\AsyncEventDispatcher\EventReceiver;

include_once __DIR__ . '/../autoload.php';

// Check require packages
if (!class_exists('AMQPConnection')) {
    \Demo::error('Please install PHP AMQP extension for run this demo.');
}

// Create a connection for real storage
$connection = new AMQPConnection();
$adapter = new AmqpAdapter($connection);

// Create proxy adapter
$proxyConnection = new AMQPConnection();
$proxyAdapter = new ProxyAmqpAdapter($adapter, $connection);

$eventReceiver = new EventReceiver($proxyAdapter);
$eventReceiver->receiveProxy();
