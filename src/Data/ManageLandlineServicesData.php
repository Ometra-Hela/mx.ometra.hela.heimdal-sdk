<?php

namespace Ometra\HeimdalSdk\Data;

class ManageLandlineServicesData extends PayloadData
{
    public function __construct(
        public readonly string $msisdn,
        public readonly string $voiceMail,
        public readonly string $msisdn_voiceMail,
        public readonly string $callForwarding,
        public readonly string $msisdn_callForwarding,
        public readonly string $unconditionalCallForwarding,
        public readonly string $msisdn_unconditionalCallForwarding,
        public readonly string $tripartiteCallWaiting,
        public readonly string $showPrivateNumbers,
    ) {
        parent::__construct();
    }
}
