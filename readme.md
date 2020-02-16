# Phetch

Phetch is the missing 80% use case HTTP client for PHP. Phetch did not invent anything new,
rather it is borrowing and building these ideas & desires:

- API is natural, comfortable, and pleasant to use
- A Zttp like client without the Guzzle dependency
- As approachable as HTTPie
- Json by default / Sensible defaults
- Has shallow stack traces
- Service container friendly

# Install

```
$ composer require ralphschindler/phetch
```

# Usage

#### GET With Query Parameters

```php
// simple call to the Github API
$resp = Phetch\Phetch::withBearerAuth('abcdefghijklmnopqrstuvwxyz0123456789')
    ->get('https://api.github.com/repos/ralphschindler/phetch/issues', ['state' => 'all']);

$resp->json(); // the response body as an array
```

#### POST with body
```php
$response = Phetch\Phetch::request()->post('https://api.github.com/repos/ralphschindler/phetch/issues', [
    'title' => 'My Issue',
    'body' => 'This is the body to my issue',
]);
```

#### PATCH
```php
$response = Phetch\Phetch::request()->patch('https://api.github.com/repos/ralphschindler/phetch/issues/1', [
    'title' => 'My Issue Updated Title!',
]);
```

#### DELETE, with special headers at call time
```php
// locking github issues requires a special accept header
$response = Phetch\Phetch::request()->delete('https://api.github.com/repos/ralphschindler/phetch/issues/1/lock',
    ['headers' => ['Accept' => 'application/vnd.github.sailor-v-preview+json']]
);
```

#### Other special helper methods

##### Setting a base url for repeated calls at the same web service:
```php
$req = Phetch\Phetch::withBaseUrl('https://api.github.com');
$respGet = $request->get(...);
$respPatch = $request->patch(...);
```

##### Without verifying the SSL Certificate
```php
$page = Phetch\Phetch::withoutVerifying()->get('https://IDidntUpdateMyCert.org');
```

##### Not following redirects
```php
$page = Phetch\Phetch::withoutRedirecting()->get('https://AUrlThatRedirects.com/place');
```

##### Basic Authentication
```php
$page = Phetch\Phetch::withBasicAuth($username, $password)->get('https://AUrlThatRedirects.com/place');
```

##### Bearer authentication
```php
$page = Phetch\Phetch::withBearerAuth($token)->get('https://AUrlThatRedirects.com/place');
```

## As A Service Inside An Application

Often times you may want to pre-configure a request to be used with shared
settings in different contexts. Additionally, you may want to build a
web-service specific client.

### Setup the service

Creating a service allows you to configure and share a `PendingRequest` object
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

- httpie command parsing
- command line phetch client
- curl adapter (good open source contribution would be nice!)
