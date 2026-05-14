<?php

namespace Ometra\HeimdalSdk\Exceptions;

use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class HeimdalSubscriberNotFoundException extends HeimdalProviderException
{
    public function __construct(HeimdalResponseDto $responseDto)
    {
        parent::__construct($responseDto);
    }
}
