<?php

namespace Ometra\HeimdalSdk\Exceptions;

use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;
use RuntimeException;

class HeimdalRequestException extends RuntimeException
{
    public readonly ?string $correlationId;

    public readonly ?string $operation;

    public readonly int $status;

    public readonly ?string $errorCode;

    public function __construct(public readonly HeimdalResponseDto $responseDto)
    {
        $this->correlationId = $responseDto->correlationId;
        $this->operation = $responseDto->operation;
        $this->status = $responseDto->status;
        $this->errorCode = $responseDto->error?->code;

        parent::__construct($responseDto->error?->message ?? 'Heimdal request failed.', $responseDto->status);
    }
}
