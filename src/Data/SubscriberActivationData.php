<?php

namespace Ometra\HeimdalSdk\Data;

class SubscriberActivationData extends PayloadData
{
    public function __construct(
        public readonly string $offeringId,
        public readonly ?string $address = null,
    ) {
        parent::__construct();
    }
}
