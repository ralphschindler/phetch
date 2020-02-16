<?php

namespace Phetch\Tests;

use Phetch\PendingRequest;
use Phetch\PhetchExeception;
use Phetch\PhetchService;
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
}
