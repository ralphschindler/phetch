<?php

namespace Phetch;

/**
 * @property-read $headers
 */
class Response
{
    use Concerns\HasHeaders;

    /** @var Request */
    protected $request;
    /** @var string */
    protected $statusLine;
    /** @var array */
    protected $headers;
    /** @var string */
    protected $body;

    public function __construct(Request $request, string $statusLine, array $headers, string $body)
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
        return isset($this->headers['Content-Type'])
            && preg_match('#^application\/[a-z0-9.+-]*json$#', $this->headers['Content-Type']);
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

    public function __get($name)
    {
        switch ($name) {
            case 'headers': return $this->headers;
        }
    }
}

