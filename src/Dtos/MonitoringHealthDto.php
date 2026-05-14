<?php

namespace Ometra\HeimdalSdk\Dtos;

final class MonitoringHealthDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        public readonly ?string $state,
        public readonly ?int $windowMinutes,
        public readonly ?int $totalCalls,
        public readonly ?int $errorCount,
        public readonly ?float $errorRate,
        public readonly ?int $slowCount,
        public readonly mixed $raw,
    ) {
        parent::__construct($attributes);
    }
}
