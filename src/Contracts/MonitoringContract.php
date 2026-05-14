<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Dtos\MonitoringHealthDto;
use Ometra\HeimdalSdk\Dtos\OperationResultDto;

class MonitoringContract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function health(int $minutes = 15): MonitoringHealthDto
    {
        return $this->mapper->monitoringHealth($this->heimdal->monitoring()->health($minutes));
    }

    public function metrics(int $minutes = 60): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->monitoring()->metrics($minutes));
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function transactions(array $filters = []): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->monitoring()->transactions($filters));
    }

    public function transaction(string $correlationId): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->monitoring()->transaction($correlationId));
    }
}
