<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Data\DateRangeQuery;
use Ometra\HeimdalSdk\Data\ImprovementPlansQuery;
use Ometra\HeimdalSdk\Dtos\DeviceInfoDto;
use Ometra\HeimdalSdk\Dtos\OperationResultDto;
use Ometra\HeimdalSdk\Dtos\View360SearchResultDto;

class View360Contract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function search(string $identifierType, string $identifierValue): View360SearchResultDto
    {
        return $this->mapper->view360Search(
            $this->heimdal->view360()->search($identifierType, $identifierValue),
            $identifierType,
            $identifierValue,
        );
    }

    public function deviceInfo(string $msisdn): DeviceInfoDto
    {
        return $this->mapper->deviceInfo($this->heimdal->view360()->deviceInfo($msisdn), $msisdn);
    }

    public function deviceInformation(string $identifierType, string $identifierValue): View360SearchResultDto
    {
        return $this->mapper->view360Search(
            $this->heimdal->view360()->deviceInformation($identifierType, $identifierValue),
            $identifierType,
            $identifierValue,
        );
    }

    public function subscriberDetails(string $identifierType, string $identifierValue): View360SearchResultDto
    {
        return $this->mapper->view360Search(
            $this->heimdal->view360()->subscriberDetails($identifierType, $identifierValue),
            $identifierType,
            $identifierValue,
        );
    }

    public function usageSummary(string $msisdn, DateRangeQuery $query): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->view360()->usageSummary($msisdn, $query->toArray()));
    }

    public function apnChange(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->view360()->apnChange($msisdn));
    }

    public function improvementPlans(ImprovementPlansQuery $query): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->view360()->improvementPlans($query));
    }
}
