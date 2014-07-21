<?php

namespace {
    /**
     * Common functions for demo environment
     */
    class Demo
    {
        /**
         * Find and include composer autoload if exists
         */
        public static function autoload()
        {
            if (!file_exists($file = __DIR__ . '/../vendor/autoload.php')) {
                if (!file_exists($file = __DIR__ . '/../../../vendor/autoload.php')) {
                    static::error(array(
                        "Autoload file not found (Composer autoload).",
                        "Please install composer.phar and run update or install command."
                    ));

                    return false;
                }
            }

            return include_once $file;
        }

        /**
         * Control stop exception
         */
        public static function registerExceptionHandler()
        {
            set_exception_handler(function ($exception) {
                if (!$exception instanceof StopException) {
                    throw $exception;
                }
            });
        }

        /**
         * Exit from demo process
         */
        public static function error($message)
        {
            if (is_array($message)) {
                $message = implode("\n", $message);
            }

            print trim($message) . "\n\n";

            throw new \StopException;
        }

        /**
         * Include common files
         */
        public static function includeCommonFiles()
        {
            include_once __DIR__ . '/StopException.php';
            include_once __DIR__ . '/DemoEvent.php';
        }

        /**
         * Check event name
         */
        public static function checkEventName($eventName)
        {
            $pattern = '/^[a-zA-Z0-9_\-]+$/';

            if (!preg_match($pattern, $eventName)) {
                \Demo::error(sprintf(
                    'Invalid event name "%s". Available: "%s"',
                    $eventName, $pattern
                ));
            }
        }

        /**
         * Check receiver key
         */
        public static function checkReceiverKey($receiverKey)
        {
            $pattern = '/^[a-zA-Z0-9_\-]+$/';

            if (!preg_match($pattern, $receiverKey)) {
                \Demo::error(sprintf(
                    'Invalid receiver key "%s". Available: "%s"',
                    $receiverKey, $pattern
                ));
            }
        }

        /**
         * Boot demo environment
         */
        public static function boot()
        {
            Demo::registerExceptionHandler();
            Demo::autoload();
            Demo::includeCommonFiles();
        }
    }

    \Demo::boot();
}