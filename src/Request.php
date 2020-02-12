<?php

namespace Phetch;

class Request
{
    /** @var string */
    protected $method;
    /** @var string */
    protected $url;
    /** @var array */
    protected $headers;
    /** @var string */
    protected $body;

    public function __construct($method, $url, $headers, $body)
    {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function method()
    {
        return $this->method;
    }

    public function url()
    {
        return $this->url;
    }

    public function headers()
    {
        return $this->headers;
    }

    public function body()
    {
        return $this->body;
    }
}

