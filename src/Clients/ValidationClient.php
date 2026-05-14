<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class ValidationClient extends AbstractClient
{
    public function numberValidation(string $msisdn, ?string $validationType = null): HeimdalResponseDto
    {
        return $this->http->post('validation/number-validation', [
            'msisdn' => $msisdn,
            'validationType' => $validationType ?? 'FORMAT',
        ]);
    }

    public function coverageCheck(string $latitude, string $longitude, ?string $technology = null): HeimdalResponseDto
    {
        return $this->http->post('network/coverage-check', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'technology' => $technology ?? '4G',
        ]);
    }
}
