<?php

namespace Ometra\HeimdalSdk\Clients;

abstract class AbstractClient
{
    public function __construct(protected readonly HeimdalHttpClient $http)
    {
    }

    protected function segment(string|int $value): string
    {
        return rawurlencode((string) $value);
    }
}
