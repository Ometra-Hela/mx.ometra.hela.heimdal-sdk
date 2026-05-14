<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Dtos\ImeiCompatibilityDto;
use Ometra\HeimdalSdk\Dtos\OperationResultDto;
use Ometra\HeimdalSdk\Exceptions\HeimdalIncompatibleImeiException;

class ImeisContract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function compatibility(string $imei): ImeiCompatibilityDto
    {
        return $this->mapper->imeiCompatibility($this->heimdal->imei()->compatibility($imei));
    }

    public function assertCompatible(string $imei): ImeiCompatibilityDto
    {
        $result = $this->compatibility($imei);

        if (! $result->compatible) {
            throw new HeimdalIncompatibleImeiException($result);
        }

        return $result;
    }

    public function lock(string $imei): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->imei()->lock($imei));
    }

    public function unlock(string $imei): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->imei()->unlock($imei));
    }

    public function status(string $imei): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->imei()->status($imei));
    }
}
