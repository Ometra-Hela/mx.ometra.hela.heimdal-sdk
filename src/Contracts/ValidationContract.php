<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Dtos\CoverageCheckResultDto;
use Ometra\HeimdalSdk\Dtos\NumberValidationResultDto;

class ValidationContract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function numberValidation(string $msisdn, ?string $validationType = null): NumberValidationResultDto
    {
        $type = $validationType ?? 'FORMAT';

        return $this->mapper->numberValidation(
            $this->heimdal->validation()->numberValidation($msisdn, $type),
            $msisdn,
            $type,
        );
    }

    public function coverageCheck(string $latitude, string $longitude, ?string $technology = null): CoverageCheckResultDto
    {
        $network = $technology ?? '4G';

        return $this->mapper->coverage(
            $this->heimdal->validation()->coverageCheck($latitude, $longitude, $network),
            $latitude,
            $longitude,
            $network,
        );
    }
}
