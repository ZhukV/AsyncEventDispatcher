#!/usr/bin/env php
<?php

use Ideea\AsyncEventDispatcher\Adapter\Amqp\AmqpAdapter;
use Ideea\AsyncEventDispatcher\Adapter\Amqp\ProxyAmqpAdapter;
use Ideea\AsyncEventDispatcher\EventDispatcher;

include_once __DIR__ . '/../autoload.php';

// Check require packages
if (!class_exists('AMQPConnection')) {
    \Demo::error('Please install PHP AMQP extension for run this demo.');
}

// Check event name
if (!isset($argv[1])) {
    \Demo::error(array(
        'Please set the event name in first argument of script.',
        '',
        'Example:',
        './demo/amqp/sender.php event_name'
    ));
}

$eventName = $argv[1];

\Demo::checkEventName($eventName);

// Create a connection for real storage
$connection = new AMQPConnection();
$adapter = new AmqpAdapter($connection);

// Create proxy adapter
$proxyConnection = new AMQPConnection();
$proxyAdapter = new ProxyAmqpAdapter($adapter, $connection);

$eventDispatcher = new EventDispatcher($proxyAdapter);
$eventDispatcher->dispatch($eventName);