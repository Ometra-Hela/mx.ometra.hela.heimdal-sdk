<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Data\PortInData;
use Ometra\HeimdalSdk\Data\PortOutData;
use Ometra\HeimdalSdk\Data\ReversePortInData;
use Ometra\HeimdalSdk\Data\ReversePortOutData;
use Ometra\HeimdalSdk\Dtos\OperationResultDto;

class MsisdnsContract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function portIn(PortInData $data): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->msisdns()->portIn($data));
    }

    public function portOut(PortOutData $data): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->msisdns()->portOut($data));
    }

    public function reversePortIn(ReversePortInData $data): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->msisdns()->reversePortIn($data));
    }

    public function reversePortOut(ReversePortOutData $data): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->msisdns()->reversePortOut($data));
    }

    public function expiredPortOut(string $msisdnPorted): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->msisdns()->expiredPortOut($msisdnPorted));
    }
}
