<?php

namespace Ometra\HeimdalSdk\Dtos;

final class SubscriberMsisdnChangeResultDto extends OperationResultDto
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        bool $success,
        ?string $operation,
        ?string $correlationId,
        ?int $providerStatus,
        mixed $raw,
        public readonly string $oldMsisdn,
        public readonly ?string $newMsisdn,
    ) {
        parent::__construct($attributes, $success, $operation, $correlationId, $providerStatus, $raw);
    }
}
