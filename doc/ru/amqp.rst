Async Event Dispatcher - AMQP
=============================

С помощью этого провайдера, Вы можете отправлять / получать события используя Amqp протокол.

Пример создания порвайдера:

.. code-block:: php

    <?php

    use Ideea\AsyncEventDispatcher\Adapter\Amqp\AmqpProvider;

    // Создание Amqp коннекта
    $amqpConnection = new \AMQPConnection();
    $amqpConnection->connect();

    $amqpChannel = new \AMQPChannel($amqpConnection);

    $adapter = new AmqpProvider($adapter);


При этом. все необходимые обменники и списки будут созданы при необходимости.

Также, Вы можете изменить базовые параметры создание обменника/списка:

* setExchangeName - установить название обменника (По умолчанию **async_event_dispatcher**)
* setExchangeType - только **direct** или **topic**
* setExchangeFlags - флаги, используемые при объявлении обменника
* setConsumerFlags - флаги, используемые при запуске получателя сообщений
* setQueuePrefix - префикс названий сообщений

AMQP Proxy
----------

При помощи **ProxyAmqpAdapter** Вы сможете проксировать ивенты, используя при этом совсем другой коннект.
Это может понадобится, если центральная система хранения/раздачи ивентов находится не на том же сервере, где и само ПО,
которое его использует.

Пример создания прокси:

.. code-block:: php

    <?php

    use Ideea\AsyncEventDispatcher\Adapter\Amqp\AmqpProvider;
    use Ideea\AsyncEventDispatcher\Adapter\Amqp\ProxyAmqpAdapter;

    // Создание рельного коннекта, туда, где находится центральная система
    $connection = new \AMQPConnection();
    $adapter = new \AMQPAdapter($connection);

    // Создаем коннекшин для прокси
    $proxyConnection = new \AMQPConnection();
    $proxyAdapter = new ProxyAmqpAdapter($adapter, $proxyConnection);


..

    **Внимание:** как видно, с кода, что коннект для центральноя системы не создается (то есть, соединение с сервером
    не открывается). Так и должно быть. Соединение будет открыто в момент запуска получателя (receiver), то есть,
    именно в том моменте, когда он нужен.


Пример запуска получателя для прокси для перенаправляения ивентов:

.. code-block:: php

    <?php

    use Ideea\AsyncEventDispatcher\EventReceiver;

    $eventReceiver = new EventReceiver($proxyAdapter);
    $eventReceiver->receiveProxy();


..

    **Внимание:** если Вы попытаетесь таким образом запустить обычный адаптер, будет выброшена ошибка.