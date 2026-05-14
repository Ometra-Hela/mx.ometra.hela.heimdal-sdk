<?php

namespace Ometra\HeimdalSdk;

use Ometra\HeimdalSdk\Clients\BatchClient;
use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Clients\HeimdalHttpClient;
use Ometra\HeimdalSdk\Clients\ImeiClient;
use Ometra\HeimdalSdk\Clients\LandlineClient;
use Ometra\HeimdalSdk\Clients\MonitoringClient;
use Ometra\HeimdalSdk\Clients\MsisdnsClient;
use Ometra\HeimdalSdk\Clients\OrdersClient;
use Ometra\HeimdalSdk\Clients\ProductsClient;
use Ometra\HeimdalSdk\Clients\SubscribersClient;
use Ometra\HeimdalSdk\Clients\ValidationClient;
use Ometra\HeimdalSdk\Clients\View360Client;
use Ometra\HeimdalSdk\Contracts\HeimdalContracts;

class HeimdalSdk
{
    private ?HeimdalClient $heimdal = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(private readonly array $config)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function config(): array
    {
        return $this->config;
    }

    public function heimdal(): HeimdalClient
    {
        return $this->heimdal ??= new HeimdalClient(new HeimdalHttpClient($this->config));
    }

    public function imei(): ImeiClient
    {
        return $this->heimdal()->imei();
    }

    public function subscribers(): SubscribersClient
    {
        return $this->heimdal()->subscribers();
    }

    public function products(): ProductsClient
    {
        return $this->heimdal()->products();
    }

    public function orders(): OrdersClient
    {
        return $this->heimdal()->orders();
    }

    public function landline(): LandlineClient
    {
        return $this->heimdal()->landline();
    }

    public function view360(): View360Client
    {
        return $this->heimdal()->view360();
    }

    public function msisdns(): MsisdnsClient
    {
        return $this->heimdal()->msisdns();
    }

    public function batch(): BatchClient
    {
        return $this->heimdal()->batch();
    }

    public function monitoring(): MonitoringClient
    {
        return $this->heimdal()->monitoring();
    }

    public function validation(): ValidationClient
    {
        return $this->heimdal()->validation();
    }

    public function contracts(): HeimdalContracts
    {
        return $this->heimdal()->contracts();
    }

    public function baseUrl(): string
    {
        return $this->heimdal()->baseUrl();
    }

    public function token(): ?string
    {
        return $this->heimdal()->token();
    }
}
