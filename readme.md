# Phetch

Phetch is the missing 80% use case HTTP client for PHP. The API is
comfortable and pleasant to use. Phetch did not invent anything new,
rather it is borrowing and building on other great projects to inform
its feature set, specifically Zttp and Httpie.

# Install

```
$ composer require ralphschindler/phetch
```

# Usage

## Standalone

For one off usage, use the `Phetch\Phetch` utility class to get started:

```php
// simple call to the Github API
$resp = Phetch\Phetch::withBearerAuth('abcdefghijklmnopqrstuvwxyz0123456789')
    ->get('https://api.github.com/repos/ralphschindler/phetch/issues', ['state' => 'all']);

$resp->json(); // the response as an array
```

Simple call to a website that has an un-verifyable certificate

```php

$page = Phetch\Phetch::withoutVerifying()->get('https://IDidntUpdateMyCert.org')->body();
```

## As A Service Inside An Application

Often times you may want to pre-configure a request to be used with shared
settings in different contexts. Additionally, you may want to build a
web-service specific client.

(These examples assume you have a `$container`)

### Setup the service

```php

$container->share('github-web-service', Phetch\Phetch::createService(function ($pendingRequestPrototype) {
    $pendingRequestPrototype->withHeaders(['User-Agent' => 'My Applications Http Client v1.0.0'])
        ->withBaseUrl('https://api.github.com')
        ->withBearerAuth('abcdefghijklmnopqrstuvwxyz0123456789');
}));
```

### Use the service somewhere in your application code (controller, service classes, etc)

```php
/** @var \Phetch\PhetchService $github */
$github = $container->get('github-web-service');

// request() will always produce a fresh & pre-configured \Phetch\PendingRequest
$resp = $github->request()
    ->get('/repos/ralphschindler/phetch/issues', ['state' => 'closed']);
```


## Todo


- httpie parser
