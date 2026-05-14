<?php

namespace Ometra\HeimdalSdk\Clients;

use Ometra\HeimdalSdk\Contracts\HeimdalContracts;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class HeimdalClient
{
    private ?ImeiClient $imei = null;

    private ?SubscribersClient $subscribers = null;

    private ?ProductsClient $products = null;

    private ?OrdersClient $orders = null;

    private ?LandlineClient $landline = null;

    private ?View360Client $view360 = null;

    private ?MsisdnsClient $msisdns = null;

    private ?BatchClient $batch = null;

    private ?MonitoringClient $monitoring = null;

    private ?ValidationClient $validation = null;

    private ?HeimdalContracts $contracts = null;

    public function __construct(private readonly HeimdalHttpClient $http)
    {
    }

    public function withCorrelationId(string $correlationId): self
    {
        return new self($this->http->withCorrelationId($correlationId));
    }

    public function withToken(string $token): self
    {
        return new self($this->http->withToken($token));
    }

    public function withoutThrowing(): self
    {
        return new self($this->http->withoutThrowing());
    }

    public function throwing(): self
    {
        return new self($this->http->throwing());
    }

    public function baseUrl(): string
    {
        return $this->http->baseUrl();
    }

    public function token(): ?string
    {
        return $this->http->token();
    }

    /**
     * @param array<string, mixed> $query
     */
    public function get(string $uri, array $query = []): HeimdalResponseDto
    {
        return $this->http->get($uri, $query);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function post(string $uri, array $data = []): HeimdalResponseDto
    {
        return $this->http->post($uri, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function patch(string $uri, array $data = []): HeimdalResponseDto
    {
        return $this->http->patch($uri, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function delete(string $uri, array $data = []): HeimdalResponseDto
    {
        return $this->http->delete($uri, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function raw(string $method, string $uri, array $data = []): HeimdalResponseDto
    {
        return $this->http->raw($method, $uri, $data);
    }

    public function imei(): ImeiClient
    {
        return $this->imei ??= new ImeiClient($this->http);
    }

    public function subscribers(): SubscribersClient
    {
        return $this->subscribers ??= new SubscribersClient($this->http);
    }

    public function products(): ProductsClient
    {
        return $this->products ??= new ProductsClient($this->http);
    }

    public function orders(): OrdersClient
    {
        return $this->orders ??= new OrdersClient($this->http);
    }

    public function landline(): LandlineClient
    {
        return $this->landline ??= new LandlineClient($this->http);
    }

    public function view360(): View360Client
    {
        return $this->view360 ??= new View360Client($this->http);
    }

    public function msisdns(): MsisdnsClient
    {
        return $this->msisdns ??= new MsisdnsClient($this->http);
    }

    public function batch(): BatchClient
    {
        return $this->batch ??= new BatchClient($this->http);
    }

    public function monitoring(): MonitoringClient
    {
        return $this->monitoring ??= new MonitoringClient($this->http);
    }

    public function validation(): ValidationClient
    {
        return $this->validation ??= new ValidationClient($this->http);
    }

    public function contracts(): HeimdalContracts
    {
        return $this->contracts ??= new HeimdalContracts($this);
    }
}
