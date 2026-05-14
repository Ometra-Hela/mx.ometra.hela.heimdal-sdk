<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Data\PurchaseProductData;
use Ometra\HeimdalSdk\Data\RemoveProductData;
use Ometra\HeimdalSdk\Dtos\OperationResultDto;

class ProductsContract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function purchase(PurchaseProductData $data): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->products()->purchase($data));
    }

    public function topup(string $msisdn, string $offeringId): OperationResultDto
    {
        return $this->purchase(new PurchaseProductData($msisdn, [$offeringId]));
    }

    public function remove(RemoveProductData $data): OperationResultDto
    {
        return $this->mapper->operation($this->heimdal->products()->remove($data));
    }
}
