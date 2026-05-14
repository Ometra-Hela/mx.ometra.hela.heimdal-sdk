<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class OrdersClient extends AbstractClient
{
    public function scheduled(string $beId, string $msisdn): HeimdalResponseDto
    {
        return $this->http->get('scheduledOrders', [
            'be_id' => $beId,
            'msisdn' => $msisdn,
        ]);
    }

    public function cancelScheduled(string|int $orderId, string $msisdn): HeimdalResponseDto
    {
        return $this->http->post("scheduledOrders/{$this->segment($orderId)}/cancel", ['msisdn' => $msisdn]);
    }

    public function status(string|int $orderId): HeimdalResponseDto
    {
        return $this->http->get("orders/{$this->segment($orderId)}");
    }
}
