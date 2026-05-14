<?php

namespace Ometra\HeimdalSdk\Data;

class PortOutData extends PayloadData
{
    public function __construct(
        public readonly string $msisdn,
        public readonly string $imsi,
        public readonly string $approvedDateABD,
        public readonly string $dida,
        public readonly string $rida,
        public readonly string $dcr,
        public readonly string $rcr,
    ) {
        parent::__construct();
    }
}
