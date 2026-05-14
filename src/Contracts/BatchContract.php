<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Dtos\BatchSubmissionDto;

class BatchContract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function imeiValidations(mixed $input, ?string $validationType = null): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->imeiValidations($input, $validationType));
    }

    public function imeiLocks(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->imeiLocks($input));
    }

    public function imeiUnlocks(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->imeiUnlocks($input));
    }

    public function subscriberActivations(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberActivations($input));
    }

    public function subscriberSuspends(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberSuspends($input));
    }

    public function subscriberResumes(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberResumes($input));
    }

    public function subscriberDeactivates(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberDeactivates($input));
    }

    public function subscriberPredeactivates(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberPredeactivates($input));
    }

    public function subscriberReactivates(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberReactivates($input));
    }

    public function subscriberChangesPrimary(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberChangesPrimary($input));
    }

    public function subscriberChangesSim(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberChangesSim($input));
    }

    public function subscriberChangesMsisdn(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberChangesMsisdn($input));
    }

    public function subscriberPurchasesSupplementary(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberPurchasesSupplementary($input));
    }

    public function subscriberBarrings(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberBarrings($input));
    }

    public function subscriberUnbarrings(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberUnbarrings($input));
    }

    public function subscriberPreregistrations(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberPreregistrations($input));
    }

    public function landlineManagements(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->landlineManagements($input));
    }
}
