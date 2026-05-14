<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class MonitoringClient extends AbstractClient
{
    public function health(int $minutes = 15): HeimdalResponseDto
    {
        return $this->http->get('monitoring/health', ['minutes' => $minutes]);
    }

    public function metrics(int $minutes = 60): HeimdalResponseDto
    {
        return $this->http->get('monitoring/metrics', ['minutes' => $minutes]);
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function transactions(array $filters = []): HeimdalResponseDto
    {
        return $this->http->get('monitoring/transactions', $filters);
    }

    public function transaction(string $correlationId): HeimdalResponseDto
    {
        return $this->http->get("monitoring/transactions/{$this->segment($correlationId)}");
    }
}
