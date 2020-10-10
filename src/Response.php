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
    /** @var array */
    protected $status;

    /** @var array */
    protected $headers;
    /** @var string */
    protected $body;

    public function __construct(Request $request, array $status, array $headers, string $body)
    {
        $this->request = $request;
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;

        if (array_diff_key(['version' => null, 'code' => null, 'reason' => null], $status) !== []) {
            throw new PhetchExeception(__CLASS__ . ' must be constructed with an array of at least "version", "code", and "reason" keys');
        }
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function httpVersion(): string
    {
        return $this->status['version'];
    }

    public function status(): int
    {
        return (int) $this->status['code'];
    }

    public function reason(): string
    {
        return (string) $this->status['reason'];
    }

    public function ok(): bool
    {
        return $this->status() === 200;
    }

    public function successful(): bool
    {
        $status = $this->status();

        return ($status >= 200) && ($status < 300);
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

