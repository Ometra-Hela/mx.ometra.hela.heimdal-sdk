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

    public function subscriberSuspends(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberSuspends($input));
    }

    public function subscriberResumes(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberResumes($input));
    }

    public function subscriberActivations(mixed $input): BatchSubmissionDto
    {
        return $this->mapper->batch($this->heimdal->batch()->subscriberActivations($input));
    }
}
