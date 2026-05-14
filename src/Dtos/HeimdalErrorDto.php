<?php

namespace Ometra\HeimdalSdk\Dtos;

final class HeimdalErrorDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        public readonly string $type,
        public readonly string $code,
        public readonly string $message,
        public readonly mixed $detail = null,
        public readonly mixed $provider = null,
    ) {
        parent::__construct($attributes);
    }

    public static function from(mixed $payload): static
    {
        $attributes = self::normalize($payload);

        return new self(
            attributes: $attributes,
            type: (string) ($attributes['type'] ?? 'unknown_error'),
            code: (string) ($attributes['code'] ?? ''),
            message: (string) ($attributes['message'] ?? 'Heimdal request failed.'),
            detail: $attributes['detail'] ?? null,
            provider: $attributes['provider'] ?? null,
        );
    }
}
