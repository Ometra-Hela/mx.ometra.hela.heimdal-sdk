<?php

namespace Ometra\HeimdalSdk\Dtos;

final class CoverageCheckResultDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        public readonly string $latitude,
        public readonly string $longitude,
        public readonly string $technology,
        public readonly mixed $data,
        public readonly mixed $raw,
    ) {
        parent::__construct($attributes);
    }
}
