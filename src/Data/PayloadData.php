<?php

namespace Ometra\HeimdalSdk\Data;

use Ometra\HeimdalSdk\Dtos\DataTransferObject;

abstract class PayloadData extends DataTransferObject
{
    public function __construct()
    {
        parent::__construct([]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        foreach (get_object_vars($this) as $key => $value) {
            if ($key === 'attributes' || $value === null) {
                continue;
            }

            $payload[$key] = $this->serializePayloadValue($value);
        }

        return $payload;
    }

    private function serializePayloadValue(mixed $value): mixed
    {
        if ($value instanceof DataTransferObject) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map(fn (mixed $item) => $this->serializePayloadValue($item), $value);
        }

        return $value;
    }
}
