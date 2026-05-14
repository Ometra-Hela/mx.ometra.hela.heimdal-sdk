<?php

namespace Ometra\HeimdalSdk\Data;

class ChangeSimData extends PayloadData
{
    public function __construct(public readonly string $newIccid)
    {
        parent::__construct();
    }

    public static function fromFullIccid(string $iccid): self
    {
        return new self(substr($iccid, 0, -1));
    }

    public static function fromAltanIccid(string $iccid): self
    {
        return new self($iccid);
    }
}
