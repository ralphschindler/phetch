<?php
namespace Phetch\Adapter;

use Phetch\Request;
use Phetch\Response;

class TestAdapter
{
    public $responseStatusLine;
    public $responseHeaders;
    public $responseBody;

    public function send(Request $request, array $options): Response
    {
        return new Response(
            $request,
            $this->responseStatusLine,
            $this->responseHeaders,
            $this->responseBody
        );
    }
}

