<?php

namespace Ometra\HeimdalSdk\Data;

class ReversePortOutData extends PayloadData
{
    public function __construct(
        public readonly string $imsi,
        public readonly string $approvedDateABD,
        public readonly string $dida,
        public readonly string $rida,
        public readonly string $dcr,
        public readonly string $rcr,
        public readonly ?string $msisdnPorted = null,
    ) {
        parent::__construct();
    }
}
