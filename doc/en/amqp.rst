Async Event Dispatcher - AMQP
=============================

Through this provider, you can send / receive events by using Amqp protocol.
Here is an example of creating a provider:

.. code-block:: php

    <?php

    use Ideea\AsyncEventDispatcher\Adapter\Amqp\AmqpProvider;

    // Creating an Amqp Connection
    $amqpConnection = new \AMQPConnection();
    $amqpConnection->connect();

    $amqpChannel = new \AMQPChannel($amqpConnection);

    $adapter = new AmqpProvider($adapter);



Thus, all the required exchangers and the lists will be created if necessary.

Also, you can change the basic parameters of the creation of the exchanger / the list:

* setExchangeName - set name exchanger (Default: **async_event_dispatcher**)
* setExchangeType - only **direct** or **topic**
* setExchangeFlags - flags that are used when you declare of the exchanger
* setConsumerFlags - flags used when you start the message recipient
* setQueuePrefix - prefix titles messages


AMQP Proxy
----------

With **ProxyAmqpAdapter** you can proxate events, using the quite different connect. This can be useful if the central
system storage/distribution of  Events is not on the same server as the software itself, which uses it.

Example of creating a proxy:

.. code-block:: php

    <?php

    use Ideea\AsyncEventDispatcher\Adapter\Amqp\AmqpProvider;
    use Ideea\AsyncEventDispatcher\Adapter\Amqp\ProxyAmqpAdapter;

    // Creating a real Connect, where there is a central system
    $connection = new \AMQPConnection();
    $adapter = new \AMQPAdapter($connection);

    // Creating a connection for the proxy
    $proxyConnection = new \AMQPConnection();
    $proxyAdapter = new ProxyAmqpAdapter($adapter, $proxyConnection);

..

    **Note:** as you can see from the code that the connection to the central system is not created (ie, the server
    connection is not open.) So it should be. Connection will be opened at the time of launch of the recipient (receiver),
    ie, it is in that moment when it is needed.


Example of the recipient to run the proxy for redirect's Event:

.. code-block:: php

    <?php

    use Ideea\AsyncEventDispatcher\EventReceiver;

    $eventReceiver = new EventReceiver($proxyAdapter);
    $eventReceiver->receiveProxy();

    Warning: if you try so run normal adapter error will be ejected.
