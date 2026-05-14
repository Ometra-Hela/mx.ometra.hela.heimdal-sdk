<?php

namespace Ometra\HeimdalSdk\Data;

class PurchaseProductData extends PayloadData
{
    /**
     * @param array<int, string> $offerings
     */
    public function __construct(
        public readonly string $msisdn,
        public readonly array $offerings,
        public readonly ?string $startEffectiveDate = null,
        public readonly ?string $expireEffectiveDate = null,
        public readonly ?string $scheduleDate = null,
        public readonly ?string $allowPurchaseOnSuspendBarring = null,
    ) {
        parent::__construct();
    }
}
