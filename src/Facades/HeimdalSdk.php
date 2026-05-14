<?php

namespace Ometra\HeimdalSdk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ometra\HeimdalSdk\Clients\HeimdalClient heimdal()
 * @method static \Ometra\HeimdalSdk\Clients\ImeiClient imei()
 * @method static \Ometra\HeimdalSdk\Clients\SubscribersClient subscribers()
 * @method static \Ometra\HeimdalSdk\Clients\BatchClient batch()
 * @method static \Ometra\HeimdalSdk\Clients\MonitoringClient monitoring()
 */
class HeimdalSdk extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'heimdal-sdk';
    }
}
