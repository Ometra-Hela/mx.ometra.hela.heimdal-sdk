<?php

namespace Ometra\HeimdalSdk\Dtos;

final class HistoryCollectionDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     * @param array<int, mixed> $items
     * @param array<string, mixed> $dateRange
     */
    public function __construct(
        array $attributes,
        public readonly string $kind,
        public readonly string $msisdn,
        public readonly array $items,
        public readonly int $count,
        public readonly array $dateRange,
        public readonly mixed $raw,
    ) {
        parent::__construct($attributes);
    }
}
