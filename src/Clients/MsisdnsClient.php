<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Data\PortInData;
use Ometra\HeimdalSdk\Data\PortOutData;
use Ometra\HeimdalSdk\Data\ReversePortInData;
use Ometra\HeimdalSdk\Data\ReversePortOutData;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class MsisdnsClient extends AbstractClient
{
    public function portIn(PortInData $data): HeimdalResponseDto
    {
        return $this->http->post('msisdns/port-in-c', $data->toArray());
    }

    public function portOut(PortOutData $data): HeimdalResponseDto
    {
        return $this->http->post('msisdns/port-out-c', $data->toArray());
    }

    public function reversePortIn(ReversePortInData $data): HeimdalResponseDto
    {
        return $this->http->post('msisdns/reverse-port-in-c', $data->toArray());
    }

    public function reversePortOut(ReversePortOutData $data): HeimdalResponseDto
    {
        return $this->http->post('msisdns/reverse-port-out-c', $data->toArray());
    }

    public function expiredPortOut(string $msisdnPorted): HeimdalResponseDto
    {
        return $this->http->post('msisdns/expired-port-out-c', ['msisdnPorted' => $msisdnPorted]);
    }
}
