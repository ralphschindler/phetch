<?php
namespace Phetch\Adapter;

use Phetch\PhetchExeception;
use Phetch\Request;
use Phetch\Response;

class PhpStreamAdapter
{
    public function send(Request $request, array $options): Response
    {
        $errors = [];

        $contextOptions = [
            'http' => [
                'method' => $request->method(),
                'ignore_errors' => true,
                'header' => $this->stringifyAssocHeaders($request->headers())
            ]
        ];

        if ($body = $request->body()) {
            $contextOptions['http']['content'] = $body;
        }

        if (isset($options['verify']) && $options['verify'] === false) {
            $contextOptions['ssl']['verify_peer'] = false;
        }

        /**
         * file_get_contents() will issue PHP warnings, these should be converted to exceptions thus
         * set_error_handler() / restore_error_handler() is used
         */
        set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) use (&$errors) {
            $errors[] = $errstr;
        });

        $body = file_get_contents($request->url(), false, stream_context_create($contextOptions));

        restore_error_handler();

        if ($body === false && $errors) {
            throw new PhetchExeception(implode('; ', $errors));
        }

        return new Response(
            $request,
            $http_response_header[0],
            $this->headerLinesToAssocHeaders(array_slice($http_response_header, 1)),
            $body
        );
    }

    protected function stringifyAssocHeaders(array $headers)
    {
        $header = '';

        foreach ($headers as $key => $value) {
            $header .= $key . ': ' . $value . "\r\n";
        }

        return rtrim($header);
    }

    protected function headerLinesToAssocHeaders(array $headerLines)
    {
        return array_column(array_map(function ($headerLine) {
            return preg_split('#:\s+#', $headerLine, 2);
        }, $headerLines), 1, 0);
    }
}

