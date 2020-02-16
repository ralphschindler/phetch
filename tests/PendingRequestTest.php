<?php

namespace Phetch\Tests;

use Phetch\Adapter\TestAdapter;
use Phetch\PendingRequest;
use Phetch\PhetchExeception;
use Phetch\PhetchService;
use Phetch\Response;
use PHPUnit\Framework\TestCase;

class PendingRequestTest extends TestCase
{
    protected $defaultHeaders = ['Accept' => '*/*', 'User-Agent' => 'Phetch/v0.0.1'];

    public function testWithHeadersValid()
    {
        $pendingRequest = (new PendingRequest)
            ->withHeaders(['Foo-bar' => 'bar']);

        $this->assertEquals(['Foo-Bar' => 'bar'] + $this->defaultHeaders, $pendingRequest->headers);

        foreach (['Foo-Bar', 'foo-bar', 'Foo_Bar'] as $testName) {
            $this->assertEquals('bar', $pendingRequest->header($testName));
        }

        $pendingRequest = (new PendingRequest)
            ->withHeaders(['Foo' => 'bar', 'BAM' => 'baz', 'BOOM' => 'boom']);

        $this->assertEquals(
            ['Foo' => 'bar', 'BAM' => 'baz', 'BOOM' => 'boom'] + $this->defaultHeaders,
            $pendingRequest->headers
        );
    }

    /**
     * @dataProvider withHeadersExceptionalData
     */
    public function testWithHeadersInvalid($headers)
    {
        $this->expectException(PhetchExeception::class);

        (new PendingRequest)->withHeaders($headers);
    }

    public function withHeadersExceptionalData()
    {
        return [
            [[0 => 'Foo']],
            [['0Foo' => 'bar']],
            [[null => 'bar']]
        ];
    }

    public function testGet()
    {
        $pendingRequest = (new PendingRequest)->withAdapter(new TestAdapter);

        $resp = $pendingRequest->get('http://example.com/foo');

        $this->assertInstanceOf(Response::class, $resp);

        $req = $resp->request();

        $this->assertEquals('GET', $req->method());
        $this->assertEquals('http://example.com/foo', $req->url());
        $this->assertEquals(['Accept' => '*/*', 'User-Agent' => 'Phetch/v0.0.1'], $req->headers());
        $this->assertEmpty($req->body());

        $resp = $pendingRequest->get('http://example.com/foo', ['a' => 'b', 'c' => 'd']);

        $req = $resp->request();

        $this->assertEquals('http://example.com/foo?a=b&c=d', $req->url());
    }
}
