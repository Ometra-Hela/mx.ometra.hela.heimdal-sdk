<?php

namespace Ometra\HeimdalSdk\Data;

class ChangeMsisdnData extends PayloadData
{
    public function __construct(
        public readonly string $zone,
        public readonly ?string $msisdnType = null,
    ) {
        parent::__construct();
    }
}
