<?php

namespace Ometra\HeimdalSdk\Data;

class RemoveProductData extends PayloadData
{
    public function __construct(
        public readonly string $msisdn,
        public readonly string $offeringId,
    ) {
        parent::__construct();
    }
}
