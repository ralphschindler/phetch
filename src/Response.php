<?php

namespace Phetch;

class Response
{
    /** @var Request */
    protected $request;
    /** @var string */
    protected $statusLine;
    /** @var array */
    protected $headers;
    /** @var string */
    protected $body;

    public function __construct(Request $request, $statusLine, $headers, $body)
    {
        $this->request = $request;
        $this->statusLine = $statusLine;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function isJson()
    {

    }

    public function header($name)
    {
        return $this->headers[$name] ?? null;
    }

    public function body()
    {
        return $this->body;
    }

    public function json()
    {
        return json_decode($this->body, true);
    }
}

