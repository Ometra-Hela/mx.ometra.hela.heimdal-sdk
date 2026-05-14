<?php

namespace Ometra\HeimdalSdk\Support;

use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;
use Ometra\HeimdalSdk\Exceptions\HeimdalForbiddenException;
use Ometra\HeimdalSdk\Exceptions\HeimdalProviderException;
use Ometra\HeimdalSdk\Exceptions\HeimdalRequestException;
use Ometra\HeimdalSdk\Exceptions\HeimdalSubscriberNotFoundException;
use Ometra\HeimdalSdk\Exceptions\HeimdalTransportException;
use Ometra\HeimdalSdk\Exceptions\HeimdalUnauthorizedException;
use Ometra\HeimdalSdk\Exceptions\HeimdalValidationException;

class ExceptionFactory
{
    public function make(HeimdalResponseDto $response): HeimdalRequestException
    {
        if ($this->isSubscriberNotFound($response)) {
            return new HeimdalSubscriberNotFoundException($response);
        }

        return match ($response->error?->type) {
            'validation_error' => new HeimdalValidationException($response),
            'unauthorized' => new HeimdalUnauthorizedException($response),
            'forbidden' => new HeimdalForbiddenException($response),
            'provider_error' => new HeimdalProviderException($response),
            'transport_error' => new HeimdalTransportException($response),
            default => $this->fromStatus($response),
        };
    }

    private function fromStatus(HeimdalResponseDto $response): HeimdalRequestException
    {
        return match ($response->status) {
            401 => new HeimdalUnauthorizedException($response),
            403 => new HeimdalForbiddenException($response),
            422 => new HeimdalValidationException($response),
            default => new HeimdalRequestException($response),
        };
    }

    private function isSubscriberNotFound(HeimdalResponseDto $response): bool
    {
        $payload = [
            'code' => $response->error?->code,
            'message' => $response->error?->message,
            'detail' => $response->error?->detail,
            'provider' => $response->error?->provider,
        ];

        $code = $this->find($payload, ['errorCode', 'code']);
        if ((string) $code === '1211000305') {
            return true;
        }

        $message = strtolower((string) ($this->find($payload, ['description', 'message', 'detail']) ?? ''));

        return str_contains($message, 'subscriber does not exist')
            || str_contains($message, 'the subscriber does not exist');
    }

    /**
     * @param array<int, string> $keys
     */
    private function find(mixed $payload, array $keys): mixed
    {
        if (! is_array($payload)) {
            return null;
        }

        foreach ($payload as $key => $value) {
            if (is_string($key) && in_array($key, $keys, true) && is_scalar($value)) {
                return $value;
            }

            $nested = $this->find($value, $keys);
            if ($nested !== null) {
                return $nested;
            }
        }

        return null;
    }
}
