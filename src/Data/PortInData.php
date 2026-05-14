<?php

namespace Ometra\HeimdalSdk\Data;

class PortInData extends PayloadData
{
    public function __construct(
        public readonly string $msisdnTransitory,
        public readonly string $msisdnPorted,
        public readonly ?string $autoScriptReg = null,
    ) {
        parent::__construct();
    }
}
