<?php

namespace Ometra\HeimdalSdk\Dtos;

final class HeimdalResponseDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     * @param array<string, array<int, string>> $headers
     */
    public function __construct(
        array $attributes,
        public readonly bool $success,
        public readonly ?string $correlationId,
        public readonly ?string $operation,
        public readonly mixed $data,
        public readonly ?HeimdalErrorDto $error,
        public readonly HeimdalMetaDto $meta,
        public readonly int $status,
        public readonly array $headers = [],
    ) {
        parent::__construct($attributes);
    }

    /**
     * @param array<string, array<int, string>> $headers
     */
    public static function fromPayload(array $payload, int $status, array $headers = []): self
    {
        $success = (bool) ($payload['success'] ?? ($status >= 200 && $status < 300));
        $errorPayload = $payload['error'] ?? null;

        return new self(
            attributes: $payload,
            success: $success,
            correlationId: self::nullableString($payload['correlation_id'] ?? $payload['correlationId'] ?? self::header($headers, 'X-Correlation-ID')),
            operation: self::nullableString($payload['operation'] ?? null),
            data: $payload['data'] ?? null,
            error: is_array($errorPayload) ? HeimdalErrorDto::from($errorPayload) : null,
            meta: HeimdalMetaDto::from($payload['meta'] ?? []),
            status: $status,
            headers: $headers,
        );
    }

    /**
     * @param array<string, array<int, string>> $headers
     */
    private static function header(array $headers, string $name): ?string
    {
        foreach ($headers as $key => $values) {
            if (strtolower($key) === strtolower($name)) {
                return $values[0] ?? null;
            }
        }

        return null;
    }
}
