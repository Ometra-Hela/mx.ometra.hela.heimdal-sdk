<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Data\ChangeMsisdnData;
use Ometra\HeimdalSdk\Data\ChangePrimaryOfferingData;
use Ometra\HeimdalSdk\Data\ChangeSimData;
use Ometra\HeimdalSdk\Data\SubscriberActivationData;
use Ometra\HeimdalSdk\Dtos\OperationResultDto;
use Ometra\HeimdalSdk\Dtos\SubscriberMsisdnChangeResultDto;
use Ometra\HeimdalSdk\Dtos\SubscriberProfileDto;

class SubscribersContract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function profile(string $msisdn): SubscriberProfileDto
    {
        return $this->mapper->profile($this->heimdal->subscribers()->profile($msisdn), $msisdn);
    }

    public function lookupForOperator(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->lookupForOperator($msisdn));
    }

    public function activate(string $msisdn, string|SubscriberActivationData $offeringId, ?string $address = null): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->activate($msisdn, $offeringId, $address));
    }

    public function preactivate(string $msisdn, string|SubscriberActivationData $offeringId, ?string $address = null): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->preactivate($msisdn, $offeringId, $address));
    }

    public function suspend(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->suspend($msisdn));
    }

    public function resume(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->resume($msisdn));
    }

    public function predeactivate(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->predeactivate($msisdn));
    }

    public function reactivate(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->reactivate($msisdn));
    }

    public function deactivate(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->deactivate($msisdn));
    }

    public function barring(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->barring($msisdn));
    }

    public function unbarring(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->unbarring($msisdn));
    }

    public function changePrimaryOffering(string $msisdn, ChangePrimaryOfferingData $data): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->changePrimaryOffering($msisdn, $data));
    }

    public function changeSim(string $msisdn, ChangeSimData $data): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->changeSim($msisdn, $data));
    }

    public function changeMsisdn(string $msisdn, ChangeMsisdnData $data): SubscriberMsisdnChangeResultDto
    {
        return $this->mapper->msisdnChange($this->heimdal->subscribers()->changeMsisdn($msisdn, $data), $msisdn);
    }

    public function status(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->status($msisdn));
    }

    public function managedServices(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->managedServices($msisdn));
    }

    public function deviceMapping(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->deviceMapping($msisdn));
    }

    public function offerings(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->offerings($msisdn));
    }

    public function preregistrations(string $msisdn): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->preregistrations($msisdn));
    }

    public function allowRecharge(string $msisdn, string $offeringId): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->subscribers()->allowRecharge($msisdn, $offeringId));
    }
}
