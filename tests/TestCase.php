<?php

namespace Ometra\HeimdalSdk\Tests;

use Ometra\HeimdalSdk\HeimdalSdkServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            HeimdalSdkServiceProvider::class,
        ];
    }
}
