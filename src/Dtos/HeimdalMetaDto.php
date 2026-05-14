<?php

namespace Ometra\HeimdalSdk\Dtos;

final class HeimdalMetaDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        public readonly ?string $provider,
        public readonly ?int $providerStatus,
        public readonly ?int $durationMs,
    ) {
        parent::__construct($attributes);
    }

    public static function from(mixed $payload): static
    {
        $attributes = self::normalize($payload);

        return new self(
            attributes: $attributes,
            provider: self::nullableString($attributes['provider'] ?? null),
            providerStatus: self::nullableInt($attributes['provider_status'] ?? $attributes['providerStatus'] ?? null),
            durationMs: self::nullableInt($attributes['duration_ms'] ?? $attributes['durationMs'] ?? null),
        );
    }
}
