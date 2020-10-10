<?php

namespace Phetch;

use Phetch\Adapter\PhpStreamAdapter;

/**
 * @property-read array $headers
 */
class PendingRequest
{
    use Concerns\HasHeaders;

    /** @var Adapter\PhpStreamAdapter|null  */
    protected $adapter = null;

    /** @var string Base URL to be used when calls to verb methods would contain relative paths */
    protected $baseUrl = '';

    /** @var array Name normalized array of header values */
    protected $headers = [
        'Accept'     => '*/*',
        'User-Agent' => 'Phetch/v0.0.1'
    ];

    protected $options = [];

    public function withAdapter($adapter): PendingRequest
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function withBaseUrl($baseUrl): PendingRequest
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function withOptions($options): PendingRequest
    {
        // @todo

        return $this;
    }

    /** @todo should share this implementation with actual Request class */
    public function header($name): ?string
    {
        return $this->headers[$this->normalizeHeaderName($name)] ?? null;
    }

    public function withHeaders(array $headers, $merge = true): PendingRequest
    {
        array_walk($headers, function ($value, $key) {
            if ($key == null || is_numeric($key) || is_numeric($key[0])) {
                throw new PhetchExeception('Header names must be strings and/or start with a character A-z');
            }
        });

        // normalize the key names
        $headers = $this->normalizeHeaders($headers);

        $this->headers = $merge ? array_merge($this->headers, $headers) : $headers;

        return $this;
    }

    public function withCookies($cookies): PendingRequest
    {
        // @todo
        throw new \RuntimeException('Not yet implemented');

        return $this;
    }

    public function withBasicAuth($username, $password): PendingRequest
    {
        // @todo
        throw new \RuntimeException('Not yet implemented');

        return $this;
    }

    public function withBearerAuth($token): PendingRequest
    {
        $this->headers['Authorization'] = 'bearer ' . $token;

        return $this;
    }

    public function withoutRedirecting(): PendingRequest
    {
        $this->options['follow_redirects'] = false;

        return $this;
    }

    public function withoutVerifying(): PendingRequest
    {
        $this->options['verify'] = false;

        return $this;
    }

    public function head($url): Response
    {
        return $this->send('HEAD', $url);
    }

    public function get($url, array $queryParameters = [], array $otherParameters = []): Response
    {
        return $this->send('GET', $url, $queryParameters,
            $otherParameters['body_parameters'] ?? [],
            $otherParameters['headers'] ?? [],
            $otherParameters['options'] ?? []
        );
    }

    public function post($url, array $bodyParameters = [], array $otherParameters = []): Response
    {
        return $this->send('POST', $url,
            [],
            $bodyParameters,
            $this->headersWithImplicitDefaultsForDataRequests($otherParameters['headers'] ?? []),
            $otherParameters['options'] ?? []
        );
    }

    public function put($url, array $bodyParameters = [], array $otherParameters = []): Response
    {
        return $this->send('PUT', $url,
            $otherParameters['query_parameters'] ?? [],
            $bodyParameters,
            $this->headersWithImplicitDefaultsForDataRequests($otherParameters['headers'] ?? []),
            $otherParameters['options'] ?? []
        );
    }

    public function patch($url, array $bodyParameters): Response
    {
        return $this->send('PATCH', $url,
            $otherParameters['query_parameters'] ?? [],
            $bodyParameters,
            $this->headersWithImplicitDefaultsForDataRequests($otherParameters['headers'] ?? []),
            $otherParameters['options'] ?? []
        );
    }

    public function delete($url, array $otherParameters = []): Response
    {
        return $this->send('DELETE', $url,
            $otherParameters['query_parameters'] ?? [],
            $otherParameters['body_parameters'] ?? [],
            $otherParameters['headers'] ?? [],
            $otherParameters['options'] ?? []
        );
    }

    public function send(string $method, string $url, array $queryParameters = [], array $bodyParameters = [], array $headers = [], array $options = []): Response
    {
        if ($this->baseUrl) {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($url);
        }

        if ($queryParameters) {
            $url = $url . '?' . http_build_query($queryParameters);
        }

        try {
            $adapter = $this->adapter ?? new PhpStreamAdapter();

            $headers = array_merge($this->headers, $this->normalizeHeaders($headers));

            $body = ($bodyParameters) ? $this->serializeBodyParameters($headers, $bodyParameters) : null;

            return $adapter->send(
                new Request($method, $url, $headers, $body),
                array_merge_recursive($this->options, $options)
            );

        } catch (\Exception $e) {
            // try to catch any adapter specific exceptions and re-throw them as
            throw new PhetchExeception(
                'Exception calling send() through ' . (isset($adapter) ? get_class($adapter) : '(adapter unknown or failed constructing)'),
                0,
                $e // nest the actual adapter::send() exception
            );
        }
    }

    protected function headersWithImplicitDefaultsForDataRequests(array $mergeHeaders = []): array
    {
        $headers = ($mergeHeaders)
            ? array_merge($this->headers, $this->normalizeHeaders($mergeHeaders))
            : $this->headers;

        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json';
        }

        if ($headers['Content-Type'] == 'application/json' && $headers['Accept'] == '*/*') {
            $headers['Accept'] = 'application/json, */*';
        }

        return $headers;
    }

    protected function serializeBodyParameters($headers, array $bodyParameters): string
    {
        if (!isset($headers['Content-Type'])
            || (isset($headers['Content-Type']) && preg_match('#^application\/[a-z0-9.+-]*json$#', $headers['Content-Type'])
            )
        ) {
            return json_encode($bodyParameters);
        }

        throw new PhetchExeception('$bodyParameters could not serialized for request');
    }

    public function __get($name)
    {
        switch ($name) {
            case 'headers': return $this->headers;
        }
    }
}

