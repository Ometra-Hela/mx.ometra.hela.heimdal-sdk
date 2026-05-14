<?php

namespace Ometra\HeimdalSdk\Data;

class ChangePrimaryOfferingData extends PayloadData
{
    public function __construct(
        public readonly string $offeringId,
        public readonly ?string $address = null,
        public readonly ?string $scheduleDate = null,
        public readonly ?string $startEffectiveDate = null,
        public readonly ?string $expireEffectiveDate = null,
        public readonly ?string $allowChangeOfferInSuspendBarring = null,
    ) {
        parent::__construct();
    }
}
