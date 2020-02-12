<?php

namespace Phetch\Adapter;

use Phetch\Request;
use Phetch\Response;

interface AdapterInterface
{
    public function send(Request $request, array $options): Response;
}

