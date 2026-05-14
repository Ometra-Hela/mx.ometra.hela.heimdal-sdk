<?php

namespace Ometra\HeimdalSdk\Dtos;

final class SubscriberProfileDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     * @param array<int, mixed> $freeUnits
     * @param array<int, mixed> $detailOfferings
     */
    public function __construct(
        array $attributes,
        public readonly ?string $msisdn,
        public readonly mixed $status,
        public readonly ?string $subStatus,
        public readonly ?string $primaryOfferingId,
        public readonly mixed $information,
        public readonly array $freeUnits,
        public readonly array $detailOfferings,
        public readonly mixed $rawSubscriber,
        public readonly mixed $raw,
    ) {
        parent::__construct($attributes);
    }
}
