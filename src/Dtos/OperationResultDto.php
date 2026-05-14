<?php

namespace Ometra\HeimdalSdk\Dtos;

class OperationResultDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        public readonly bool $success,
        public readonly ?string $operation,
        public readonly ?string $correlationId,
        public readonly ?int $providerStatus,
        public readonly mixed $raw,
    ) {
        parent::__construct($attributes);
    }

    public static function fromResponse(HeimdalResponseDto $response): self
    {
        return new self(
            attributes: self::normalize($response->data),
            success: $response->success,
            operation: $response->operation,
            correlationId: $response->correlationId,
            providerStatus: $response->meta->providerStatus,
            raw: $response->data,
        );
    }
}
