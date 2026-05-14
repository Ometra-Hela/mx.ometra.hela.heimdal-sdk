# Heimdal SDK Para Laravel

SDK Composer/Laravel para consumir Heimdal `/api/v2` desde otros modulos HELA. El paquete encapsula autenticacion bearer, `X-Correlation-ID`, envelopes normalizados, excepciones tipadas y clientes por dominio.

## Instalacion

```bash
composer require ometra/heimdal-sdk
```

Laravel descubre automaticamente el service provider y el facade.

## Configuracion

Publica el archivo de configuracion:

```bash
php artisan vendor:publish --tag=heimdal-sdk-config
```

Variables disponibles:

```dotenv
HEIMDAL_SDK_BASE_URL=https://heimdal.example.test
HEIMDAL_SDK_TOKEN=
HEIMDAL_SDK_SOURCE_APP=auster
HEIMDAL_SDK_TIMEOUT=30
HEIMDAL_SDK_RETRY_TIMES=0
HEIMDAL_SDK_RETRY_SLEEP=100
HEIMDAL_SDK_THROW=true
```

`HEIMDAL_SDK_BASE_URL` debe apuntar al host de Heimdal sin `/api`. El SDK agrega `/api/v2` automaticamente.

## Dos Niveles De Uso

El SDK expone dos superficies:

- **Raw clients**: `HeimdalSdk::subscribers()`, `HeimdalSdk::imei()`, `HeimdalSdk::batch()`, etc. Devuelven el envelope `HeimdalResponseDto` tal como lo responde Heimdal v2.
- **Contracts**: `HeimdalSdk::contracts()`. Devuelven DTOs de dominio estables y son la superficie recomendada para nuevos consumidores.

## Uso Rapido Con Contratos

```php
use Ometra\HeimdalSdk\Facades\HeimdalSdk;

$profile = HeimdalSdk::contracts()
    ->subscribers()
    ->profile('525512345678');

$profile->subStatus;
$profile->primaryOfferingId;
$profile->freeUnits;
```

## Uso Raw

```php
use Ometra\HeimdalSdk\Facades\HeimdalSdk;

$profile = HeimdalSdk::subscribers()->profile('525512345678');

if ($profile->success) {
    $data = $profile->data;
}
```

Tambien se puede resolver desde el contenedor:

```php
use Ometra\HeimdalSdk\HeimdalSdk;

$sdk = app(HeimdalSdk::class);
$sdk->heimdal()->imei()->status('123456789012345');
```

## Correlation ID

El SDK genera `X-Correlation-ID` automaticamente. Para usar uno propio:

```php
$response = HeimdalSdk::heimdal()
    ->withCorrelationId('order-1001-activate')
    ->subscribers()
    ->activate('525512345678', 'OFFER-1');
```

Para usar un token distinto en una llamada:

```php
$response = HeimdalSdk::heimdal()
    ->withToken($token)
    ->monitoring()
    ->health();
```

## Data Objects

Los payloads criticos tienen objetos tipados:

```php
use Ometra\HeimdalSdk\Data\PurchaseProductData;
use Ometra\HeimdalSdk\Facades\HeimdalSdk;

$response = HeimdalSdk::products()->purchase(
    new PurchaseProductData(
        msisdn: '525512345678',
        offerings: ['OFFER-1'],
        scheduleDate: '2026-05-13T10:00:00',
    )
);
```

Cambio de SIM:

```php
use Ometra\HeimdalSdk\Data\ChangeSimData;

HeimdalSdk::subscribers()->changeSim(
    '525512345678',
    new ChangeSimData('8952000000000000000')
);

// Remueve el ultimo caracter, util cuando el sistema conserva ICCID completo con digito/F final.
ChangeSimData::fromFullIccid('895200000000000000F');

// Envia exactamente el ICCID esperado por Altan.
ChangeSimData::fromAltanIccid('8952000000000000000');
```

## Contratos Estables

Los contratos normalizan respuestas comunes de Altan sin perder el payload original en `raw`.

```php
use Ometra\HeimdalSdk\Data\DateRangeQuery;
use Ometra\HeimdalSdk\Facades\HeimdalSdk;

$imei = HeimdalSdk::contracts()
    ->imeis()
    ->compatibility('12345678901234');

if ($imei->compatible) {
    $status = $imei->shortStatus;
}

$history = HeimdalSdk::contracts()
    ->history()
    ->sim('525512345678', new DateRangeQuery('2026-05-01', '2026-05-13', limit: 500));

$topup = HeimdalSdk::contracts()
    ->products()
    ->topup('525512345678', 'OFFER-1');
```

DTOs principales:

- `OperationResultDto`
- `SubscriberProfileDto`
- `SubscriberMsisdnChangeResultDto`
- `ImeiCompatibilityDto`
- `HistoryCollectionDto`
- `View360SearchResultDto`
- `DeviceInfoDto`
- `CoverageCheckResultDto`
- `NumberValidationResultDto`
- `BatchSubmissionDto`
- `MonitoringHealthDto`

## Batch Con Records

```php
HeimdalSdk::batch()->subscriberSuspends([
    [
        'msisdn' => '525512345678',
        'scheduleDate' => '2026-05-13T10:00:00',
    ],
]);
```

## Batch Con CSV

```php
HeimdalSdk::batch()->subscriberBarrings(storage_path('app/barrings.csv'));
```

Tambien acepta `resource` y `Illuminate\Http\UploadedFile`.

## Monitoreo

```php
$health = HeimdalSdk::monitoring()->health(minutes: 15);
$metrics = HeimdalSdk::monitoring()->metrics(minutes: 60);
$trace = HeimdalSdk::monitoring()->transaction('order-1001-activate');
```

## Manejo De Errores

Por default, el SDK lanza excepciones tipadas cuando Heimdal responde `success=false` o HTTP no exitoso:

```php
use Ometra\HeimdalSdk\Exceptions\HeimdalProviderException;
use Ometra\HeimdalSdk\Facades\HeimdalSdk;

try {
    HeimdalSdk::products()->purchase($payload);
} catch (HeimdalProviderException $e) {
    report([
        'correlation_id' => $e->correlationId,
        'operation' => $e->operation,
        'status' => $e->status,
        'code' => $e->errorCode,
    ]);
}
```

Excepciones disponibles:

- `HeimdalRequestException`
- `HeimdalValidationException`
- `HeimdalUnauthorizedException`
- `HeimdalForbiddenException`
- `HeimdalProviderException`
- `HeimdalTransportException`
- `HeimdalSubscriberNotFoundException`
- `HeimdalIncompatibleImeiException`

Para desactivar throws en una cadena:

```php
$response = HeimdalSdk::heimdal()
    ->withoutThrowing()
    ->products()
    ->remove($payload);

if (! $response->success) {
    $error = $response->error;
}
```

## Clientes Disponibles

- `imei()`
- `subscribers()`
- `products()`
- `orders()`
- `landline()`
- `view360()`
- `msisdns()`
- `batch()`
- `monitoring()`
- `validation()`
- `contracts()`

## Cliente Raw

Para endpoints v2 que aun no tengan helper:

```php
$response = HeimdalSdk::heimdal()->post('custom/path', [
    'field' => 'value',
]);

$response = HeimdalSdk::heimdal()->raw('PATCH', 'custom/path', [
    'field' => 'value',
]);
```

El cliente raw sigue usando `/api/v2`, bearer token, correlation id, normalizacion y excepciones.

## Migrating Auster

No es necesario cambiar Auster en la primera adopcion. El mapeo recomendado para reemplazar gradualmente `App\Domain\Vendor\Ometra\Hela\HeimdalAdapter` es:

| HeimdalAdapter actual | Contrato SDK recomendado |
| --- | --- |
| `getProfile()` | `HeimdalSdk::contracts()->subscribers()->profile($msisdn)` |
| `topup($offerId)` | `HeimdalSdk::contracts()->products()->topup($msisdn, $offerId)` |
| `changeSimCard($iccid)` | `HeimdalSdk::contracts()->subscribers()->changeSim($msisdn, ChangeSimData::fromFullIccid($iccid))` |
| `requestNewMsisdn($zone)` | `HeimdalSdk::contracts()->subscribers()->changeMsisdn($msisdn, new ChangeMsisdnData($zone, '1'))` |
| `checkImei($imei)` | `HeimdalSdk::contracts()->imeis()->compatibility($imei)` |
| `search($type, $value)` | `HeimdalSdk::contracts()->view360()->search($type, $value)` |
| `simHistory(...)` | `HeimdalSdk::contracts()->history()->sim($msisdn, new DateRangeQuery(...))` |
| `operationHistory(...)` | `HeimdalSdk::contracts()->history()->operation($msisdn, new DateRangeQuery(...))` |

La excepcion `HeimdalSubscriberNotFoundException` reemplaza el parseo manual de `errorCode=1211000305` y mensajes `The subscriber does not exist`.

## Pruebas

```bash
composer test
```
