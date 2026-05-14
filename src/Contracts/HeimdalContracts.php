<?php

namespace Ometra\HeimdalSdk\Contracts;

use Ometra\HeimdalSdk\Clients\HeimdalClient;
use Ometra\HeimdalSdk\Contracts\Mappers\DomainResponseMapper;

class HeimdalContracts
{
    private DomainResponseMapper $mapper;

    private ?SubscribersContract $subscribers = null;

    private ?ImeisContract $imeis = null;

    private ?HistoryContract $history = null;

    private ?View360Contract $view360 = null;

    private ?ValidationContract $validation = null;

    private ?ProductsContract $products = null;

    private ?BatchContract $batch = null;

    private ?MonitoringContract $monitoring = null;

    public function __construct(private readonly HeimdalClient $heimdal)
    {
        $this->mapper = new DomainResponseMapper();
    }

    public function subscribers(): SubscribersContract
    {
        return $this->subscribers ??= new SubscribersContract($this->heimdal, $this->mapper);
    }

    public function imeis(): ImeisContract
    {
        return $this->imeis ??= new ImeisContract($this->heimdal, $this->mapper);
    }

    public function history(): HistoryContract
    {
        return $this->history ??= new HistoryContract($this->heimdal, $this->mapper);
    }

    public function view360(): View360Contract
    {
        return $this->view360 ??= new View360Contract($this->heimdal, $this->mapper);
    }

    public function validation(): ValidationContract
    {
        return $this->validation ??= new ValidationContract($this->heimdal, $this->mapper);
    }

    public function products(): ProductsContract
    {
        return $this->products ??= new ProductsContract($this->heimdal, $this->mapper);
    }

    public function batch(): BatchContract
    {
        return $this->batch ??= new BatchContract($this->heimdal, $this->mapper);
    }

    public function monitoring(): MonitoringContract
    {
        return $this->monitoring ??= new MonitoringContract($this->heimdal, $this->mapper);
    }
}
