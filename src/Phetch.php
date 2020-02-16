<?php

namespace Phetch;

/**
 * @mixin PendingRequest
 */
class Phetch
{
    public static function createService(callable $callback): PhetchService
    {
        $pendingRequest = new PendingRequest;

        ($callback instanceof \Closure)
            ? $callback->bindTo($pendingRequest)($pendingRequest)
            : $callback($pendingRequest);

        return new PhetchService($pendingRequest);
    }

    /*
    public static function httpie($command)
    {
        // @todo
    }
    */

    /**
     * @return PendingRequest
     */
    public static function request()
    {
        return new PendingRequest;
    }

    /**
     * @param $method
     * @param $arguments
     * @return PendingRequest
     */
    public static function __callStatic($method, $arguments)
    {
        return (new PendingRequest)->{$method}(...$arguments);
    }
}
