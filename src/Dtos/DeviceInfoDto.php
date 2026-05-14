<?php

namespace Ometra\HeimdalSdk\Dtos;

final class DeviceInfoDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        public readonly ?string $msisdn,
        public readonly mixed $data,
        public readonly mixed $raw,
    ) {
        parent::__construct($attributes);
    }
}
