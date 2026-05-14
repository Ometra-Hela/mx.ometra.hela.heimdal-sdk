<?php

namespace Ometra\HeimdalSdk\Data;

class ImprovementPlansQuery extends PayloadData
{
    public function __construct(
        public readonly string $identifierType,
        public readonly string $page,
        public readonly string $limit,
        public readonly ?string $identifierValue = null,
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
    ) {
        parent::__construct();
    }
}
