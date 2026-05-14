<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Data\ImprovementPlansQuery;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class View360Client extends AbstractClient
{
    public function search(string $identifierType, string $identifierValue): HeimdalResponseDto
    {
        return $this->http->get('360/subscribers/search', [
            'identifierType' => $identifierType,
            'identifierValue' => $identifierValue,
        ]);
    }

    public function deviceInformation(string $identifierType, string $identifierValue): HeimdalResponseDto
    {
        return $this->http->get('360/subscribers/getDeviceInformation', [
            'identifierType' => $identifierType,
            'identifierValue' => $identifierValue,
        ]);
    }

    public function deviceInfo(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get('360/subscribers/device-info', [
            'identifierType' => 'msisdn',
            'identifierValue' => $msisdn,
        ]);
    }

    public function apnChange(string $msisdn): HeimdalResponseDto
    {
        return $this->http->post("360/subscribers/{$this->segment($msisdn)}/apn-change");
    }

    public function subscriberDetails(string $identifierType, string $identifierValue): HeimdalResponseDto
    {
        return $this->http->get('360/subscribers/details', [
            'identifierType' => $identifierType,
            'identifierValue' => $identifierValue,
        ]);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function usageSummary(string $msisdn, array $query): HeimdalResponseDto
    {
        return $this->http->get("360/subscribers/{$this->segment($msisdn)}/usage-summary", $query);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function history(string $msisdn, string $kind, array $query): HeimdalResponseDto
    {
        return $this->http->get("360/subscribers/{$this->segment($msisdn)}/history/{$this->segment($kind)}", $query);
    }

    public function improvementPlans(ImprovementPlansQuery $query): HeimdalResponseDto
    {
        return $this->http->get('360/network-services/improvement-plans', $query->toArray());
    }
}
