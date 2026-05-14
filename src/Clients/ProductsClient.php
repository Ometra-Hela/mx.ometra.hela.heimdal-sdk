<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Data\PurchaseProductData;
use Ometra\HeimdalSdk\Data\RemoveProductData;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class ProductsClient extends AbstractClient
{
    public function purchase(PurchaseProductData $data): HeimdalResponseDto
    {
        return $this->http->post('products/purchase', $data->toArray());
    }

    public function remove(RemoveProductData $data): HeimdalResponseDto
    {
        return $this->http->post('products/remove', $data->toArray());
    }
}
