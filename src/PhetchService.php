<?php

namespace Phetch;

class PhetchService
{
    protected $pendingRequestPrototype;

    public function __construct(PendingRequest $pendingRequestPrototype)
    {
        $this->pendingRequestPrototype = $pendingRequestPrototype;
    }

    public function request(): PendingRequest
    {
        return clone $this->pendingRequestPrototype;
    }
}

