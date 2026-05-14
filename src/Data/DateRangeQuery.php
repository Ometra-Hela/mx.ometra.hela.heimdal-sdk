<?php

namespace Ometra\HeimdalSdk\Data;

class DateRangeQuery extends PayloadData
{
    public function __construct(
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?bool $reportMode = null,
    ) {
        parent::__construct();
    }
}
