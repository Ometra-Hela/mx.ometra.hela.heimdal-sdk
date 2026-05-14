<?php

namespace Ometra\HeimdalSdk\Support;

use Illuminate\Http\Client\Response;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class ResponseFactory
{
    public function fromHttpResponse(Response $response): HeimdalResponseDto
    {
        $payload = $response->json();

        if (! is_array($payload)) {
            $payload = [
                'success' => $response->successful(),
                'data' => $response->body(),
                'meta' => [
                    'provider' => 'heimdal',
                    'provider_status' => $response->status(),
                    'duration_ms' => null,
                ],
            ];
        }

        if (! array_key_exists('success', $payload)) {
            $payload['success'] = $response->successful();
        }

        if (! $payload['success'] && ! isset($payload['error'])) {
            $payload['error'] = [
                'type' => $this->typeForStatus($response->status()),
                'code' => (string) $response->status(),
                'message' => $response->reason() ?: 'Heimdal request failed.',
                'detail' => null,
            ];
        }

        return HeimdalResponseDto::fromPayload($payload, $response->status(), $response->headers());
    }

    private function typeForStatus(int $status): string
    {
        return match ($status) {
            401 => 'unauthorized',
            403 => 'forbidden',
            422 => 'validation_error',
            default => 'transport_error',
        };
    }
}
