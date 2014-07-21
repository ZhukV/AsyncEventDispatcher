#!/usr/bin/env php
<?php

use Ideea\AsyncEventDispatcher\Adapter\Amqp\AmqpAdapter;
use Ideea\AsyncEventDispatcher\EventReceiver;

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
        './demo/amqp/receiver.php event_name'
    ));
}

$eventName = $argv[1];

if (isset($argv[2])) {
    $receiverKey = $argv[2];
    \Demo::checkReceiverKey($receiverKey);
} else {
    $receiverKey = null;
}

\Demo::checkEventName($eventName);

// Create AMQP connection and channel
$amqpConnection = new \AMQPConnection();
$amqpConnection->connect();

$channel = new AMQPChannel($amqpConnection);

// Create AMQP AsyncEventDispatcher provider
$adapter = new AmqpAdapter($channel);

// Create event receiver
$eventReceiver = new EventReceiver($adapter);
$eventReceiver->receive($eventName, function (DemoEvent $event) {
    print "[+] Complete receive event. Class: " . get_class($event) . " \n";
}, $receiverKey);