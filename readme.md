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

### Setup the service

Creating a service allows you to configure and share a PendingRequest object
with as much boilerplate as necessary for your app to talk to a particular
service. (`$container` is assumed to be some kind of service container.)

```php
$githubWebService = Phetch\Phetch::createService(function ($pendingRequestPrototype) {
    $pendingRequestPrototype->withHeaders(['User-Agent' => 'My Applications Http Client v1.0.0'])
        ->withBaseUrl('https://api.github.com')
        ->withBearerAuth('abcdefghijklmnopqrstuvwxyz0123456789');
});

$container->share('github-web-service', $githubWebService);
```

### Use the service somewhere in your application code (controller, service classes, etc)

The `PhetchService` now contains your prototypical `PendingRequest`
object that is preconfigured for use everywhere in your application. Each time
you call `$service->request()`, you will get a cloned/fresh `PendingRequest`
object that you can interact with. State changes to this new object will
not affect the service's pre-configured object:

```php
/** @var \Phetch\PhetchService $github */
$github = $container->get('github-web-service');

// request() will always produce a fresh & pre-configured \Phetch\PendingRequest
$resp = $github->request()
    ->get('/repos/ralphschindler/phetch/issues', ['state' => 'closed']);
```


## Todo


- httpie parser
