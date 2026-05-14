<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Data\ManageLandlineServicesData;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class LandlineClient extends AbstractClient
{
    public function manageServices(ManageLandlineServicesData $data): HeimdalResponseDto
    {
        return $this->http->post('landline/manage-services', $data->toArray());
    }

    public function managedServices(string $msisdn): HeimdalResponseDto
    {
        return $this->http->get("landline/{$this->segment($msisdn)}/managedServices");
    }
}
