<?php

namespace Phetch;

use Phetch\Adapter\PhpStreamAdapter;

class PendingRequest
{
    /** @var Adapter\PhpStreamAdapter|null  */
    protected $adapter = null;

    /** @var string Base URL to be used when calls to verb methods would contain relative paths */
    protected $baseUrl = '';

    protected $headers = [
        'accept' => '*/*',
        'user-agent' => 'HTTPie/v0.0.1'
    ];

    protected $options = [];

    public function withAdapter($adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function withBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function withOptions($options)
    {
        // @todo

        return $this;
    }

    public function header($name)
    {
        return $this->headers[$name] ?? null;
    }

    public function withHeaders($headers)
    {
        $this->headers = array_merge(
            $this->headers,
            array_change_key_case($headers, CASE_LOWER)
        );

        return $this;
    }

    public function withCookies($cookies)
    {
        // @todo
        throw new \RuntimeException('Not yet implemented');

        return $this;
    }

    public function withBasicAuth($username, $password)
    {
        // @todo
        throw new \RuntimeException('Not yet implemented');

        return $this;
    }

    public function withBearerAuth($token)
    {
        $this->headers['authorization'] = 'bearer ' . $token;

        return $this;
    }

    public function withoutRedirecting()
    {
        $this->options['follow_redirects'] = false;

        return $this;
    }

    public function withoutVerifying()
    {
        $this->options['verify'] = false;

        return $this;
    }

    public function head($url)
    {
        return $this->send('HEAD', $url);
    }

    public function get($url, $queryParameters = [])
    {
        return $this->send('GET', $url, $queryParameters);
    }

    public function post($url, $bodyParameters = [])
    {
        return $this->send('POST', $url, [], $bodyParameters);
    }

    public function patch($url, $bodyParameters)
    {
        return $this->send('PATCH', $url, $bodyParameters);
    }

    public function delete($url)
    {
        return $this->send('DELETE', $url);
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

            return $adapter->send(
                new Request($method, $url, array_merge_recursive($this->headers, $headers), null),
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
}

