<?php

namespace Ometra\HeimdalSdk\Exceptions;

use RuntimeException;

class MissingHeimdalConfigurationException extends RuntimeException
{
    public static function missingBaseUrl(): self
    {
        return new self('Missing Heimdal SDK base URL. Configure HEIMDAL_SDK_BASE_URL.');
    }
}
