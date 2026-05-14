<?php

namespace Ometra\HeimdalSdk\Dtos;

final class ImeiCompatibilityDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        public readonly ?string $imei,
        public readonly ?string $blocked,
        public readonly ?string $homologated,
        public readonly ?string $band28,
        public readonly ?string $volteCapable,
        public readonly bool $supportsEsim,
        public readonly bool $compatible,
        public readonly ?string $shortStatus,
        public readonly mixed $raw,
    ) {
        parent::__construct($attributes);
    }
}
