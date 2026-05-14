<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class ImeiClient extends AbstractClient
{
    public function lock(string $imei): HeimdalResponseDto
    {
        return $this->http->post("imei/{$this->segment($imei)}/lock");
    }

    public function unlock(string $imei): HeimdalResponseDto
    {
        return $this->http->post("imei/{$this->segment($imei)}/unlock");
    }

    public function status(string $imei): HeimdalResponseDto
    {
        return $this->http->get("imeis/{$this->segment($imei)}/status");
    }

    public function compatibility(string $imei): HeimdalResponseDto
    {
        return $this->http->get("imeis/{$this->segment($imei)}/compatibility");
    }
}
