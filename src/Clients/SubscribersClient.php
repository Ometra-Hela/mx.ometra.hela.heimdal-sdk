<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Data\ChangeMsisdnData;
use Ometra\HeimdalSdk\Data\ChangePrimaryOfferingData;
use Ometra\HeimdalSdk\Data\ChangeSimData;
use Ometra\HeimdalSdk\Data\SubscriberActivationData;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class SubscribersClient extends AbstractClient
{
    public function lookupForOperator(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get('subscribers/lookupForOperator', ['msisdn' => $msisdn]);
    }

    public function profile(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get("subscribers/{$this->segment($msisdn)}/profile");
    }

    public function activate(string $msisdn, string|SubscriberActivationData $offeringId, ?string $address = null): HeimdalResponseDto
    {
        return $this->http->post(
            "subscribers/{$this->segment($msisdn)}/activate",
            $this->activationPayload($offeringId, $address),
        );
    }

    public function preactivate(string $msisdn, string|SubscriberActivationData $offeringId, ?string $address = null): HeimdalResponseDto
    {
        return $this->http->post(
            "subscribers/{$this->segment($msisdn)}/preactivate",
            $this->activationPayload($offeringId, $address),
        );
    }

    public function suspend(string $msisdn): HeimdalResponseDto
    {
        return $this->statusPost($msisdn, 'suspend');
    }

    public function resume(string $msisdn): HeimdalResponseDto
    {
        return $this->statusPost($msisdn, 'resume');
    }

    public function predeactivate(string $msisdn): HeimdalResponseDto
    {
        return $this->statusPost($msisdn, 'predeactivate');
    }

    public function reactivate(string $msisdn): HeimdalResponseDto
    {
        return $this->statusPost($msisdn, 'reactivate');
    }

    public function deactivate(string $msisdn): HeimdalResponseDto
    {
        return $this->statusPost($msisdn, 'deactivate');
    }

    public function barring(string $msisdn): HeimdalResponseDto
    {
        return $this->statusPost($msisdn, 'barring');
    }

    public function unbarring(string $msisdn): HeimdalResponseDto
    {
        return $this->statusPost($msisdn, 'unbarring');
    }

    public function status(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get("subscribers/{$this->segment($msisdn)}/status");
    }

    public function managedServices(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get("subscribers/{$this->segment($msisdn)}/managed-services");
    }

    public function deviceMapping(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get("subscribers/{$this->segment($msisdn)}/device-mapping");
    }

    public function offerings(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get("subscribers/{$this->segment($msisdn)}/offerings");
    }

    public function changePrimaryOffering(string $msisdn, ChangePrimaryOfferingData $data): HeimdalResponseDto
    {
        return $this->http->patch("subscribers/{$this->segment($msisdn)}", [
            'primaryOffering' => $data->toArray(),
        ]);
    }

    public function changeSim(string $msisdn, ChangeSimData $data): HeimdalResponseDto
    {
        return $this->http->patch("subscribers/{$this->segment($msisdn)}", [
            'changeSubscriberSIM' => $data->toArray(),
        ]);
    }

    public function changeMsisdn(string $msisdn, ChangeMsisdnData $data): HeimdalResponseDto
    {
        return $this->http->patch("subscribers/{$this->segment($msisdn)}", [
            'changeSubscriberMSISDN' => $data->toArray(),
        ]);
    }

    public function preregistrations(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get("subscribers/{$this->segment($msisdn)}/preregistrations");
    }

    public function allowRecharge(string $msisdn, string $offeringId): HeimdalResponseDto
    {
        return $this->http->get("subscribers/{$this->segment($msisdn)}/allowrecharge", ['offeringId' => $offeringId]);
    }

    private function statusPost(string $msisdn, string $action): HeimdalResponseDto
    {
        return $this->http->post("subscribers/{$this->segment($msisdn)}/{$action}");
    }

    /**
     * @return array<string, mixed>
     */
    private function activationPayload(string|SubscriberActivationData $offeringId, ?string $address): array
    {
        if ($offeringId instanceof SubscriberActivationData) {
            return $offeringId->toArray();
        }

        return (new SubscriberActivationData($offeringId, $address))->toArray();
    }
}
