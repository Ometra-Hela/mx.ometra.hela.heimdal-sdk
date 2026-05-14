<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;
use Ometra\HeimdalSdk\Data\DateRangeQuery;
use Ometra\HeimdalSdk\Dtos\HistoryCollectionDto;

class HistoryContract
{
    public function __construct(
        private readonly HeimdalClient $heimdal,
        private readonly DomainResponseMapper $mapper,
    ) {
    }

    public function sim(string $msisdn, DateRangeQuery $query): HistoryCollectionDto
    {
        return $this->fetch($msisdn, 'sim', $query);
    }

    public function imei(string $msisdn, DateRangeQuery $query): HistoryCollectionDto
    {
        return $this->fetch($msisdn, 'imei', $query);
    }

    public function state(string $msisdn, DateRangeQuery $query): HistoryCollectionDto
    {
        return $this->fetch($msisdn, 'state', $query);
    }

    public function offer(string $msisdn, DateRangeQuery $query): HistoryCollectionDto
    {
        return $this->fetch($msisdn, 'offer', $query);
    }

    public function operation(string $msisdn, DateRangeQuery $query): HistoryCollectionDto
    {
        return $this->fetch($msisdn, 'operation', $query);
    }

    public function consumption(string $msisdn, DateRangeQuery $query): HistoryCollectionDto
    {
        return $this->fetch($msisdn, 'consumption', $query);
    }

    private function fetch(string $msisdn, string $kind, DateRangeQuery $query): HistoryCollectionDto
    {
        return $this->mapper->history(
            $this->heimdal->view360()->history($msisdn, $kind, $query->toArray()),
            $kind,
            $msisdn,
            $query,
        );
    }
}
