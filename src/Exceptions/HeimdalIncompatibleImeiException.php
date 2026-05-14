<?php

namespace Ometra\HeimdalSdk\Exceptions;

use Ometra\HeimdalSdk\Dtos\ImeiCompatibilityDto;
use RuntimeException;

class HeimdalIncompatibleImeiException extends RuntimeException
{
    public function __construct(public readonly ImeiCompatibilityDto $compatibility)
    {
        parent::__construct('IMEI is not compatible with the requested network.', 422);
    }
}
