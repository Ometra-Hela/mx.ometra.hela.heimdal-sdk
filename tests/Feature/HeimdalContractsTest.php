<?php

namespace Ometra\HeimdalSdk\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Ometra\HeimdalSdk\Data\ChangeMsisdnData;
use Ometra\HeimdalSdk\Data\ChangeSimData;
use Ometra\HeimdalSdk\Data\DateRangeQuery;
use Ometra\HeimdalSdk\Data\PortInData;
use Ometra\HeimdalSdk\Data\SubscriberActivationData;
use Ometra\HeimdalSdk\Dtos\BatchSubmissionDto;
use Ometra\HeimdalSdk\Dtos\HistoryCollectionDto;
use Ometra\HeimdalSdk\Dtos\ImeiCompatibilityDto;
use Ometra\HeimdalSdk\Dtos\OperationResultDto;
use Ometra\HeimdalSdk\Dtos\SubscriberProfileDto;
use Ometra\HeimdalSdk\Exceptions\HeimdalSubscriberNotFoundException;
use Ometra\HeimdalSdk\Facades\HeimdalSdk;
use Ometra\HeimdalSdk\Tests\TestCase;

class HeimdalContractsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('heimdal-sdk.base_url', 'https://heimdal.example.test/');
        $this->app['config']->set('heimdal-sdk.token', 'secret-token');
        $this->app['config']->set('heimdal-sdk.source', 'heimdal-sdk-tests');
        $this->app['config']->set('heimdal-sdk.throw', true);
    }

    public function test_contract_profile_maps_response_subscriber(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/subscribers/525512345678/profile' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-profile',
                'operation' => 'v2.subscribers.profile',
                'data' => [
                    'responseSubscriber' => [
                        'msisdn' => '525512345678',
                        'status' => ['subStatus' => 'ACTIVE'],
                        'primaryOffering' => ['offeringId' => 'OFFER-1'],
                        'information' => ['name' => 'Test'],
                        'freeUnits' => [
                            ['name' => 'DATA', 'detailOfferings' => [['offeringId' => 'BAG-1']]],
                        ],
                    ],
                ],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 10],
            ]),
        ]);

        $profile = HeimdalSdk::contracts()->subscribers()->profile('525512345678');

        $this->assertInstanceOf(SubscriberProfileDto::class, $profile);
        $this->assertSame('525512345678', $profile->msisdn);
        $this->assertSame('ACTIVE', $profile->subStatus);
        $this->assertSame('OFFER-1', $profile->primaryOfferingId);
        $this->assertCount(1, $profile->detailOfferings);
        $this->assertSame('ACTIVE', $profile->get('status')['subStatus']);
    }

    public function test_contract_imei_compatibility_maps_altan_shape(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/imeis/12345678901234/compatibility' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-imei',
                'operation' => 'v2.imei.compatibility',
                'data' => [
                    'imei' => [
                        'imei' => '12345678901234',
                        'blocked' => 'NO',
                        'homologated' => 'SI',
                        'soportaESIM' => 'SI',
                    ],
                    'deviceFeatures' => [
                        'band28' => 'SI',
                        'volteCapable' => 'SI',
                    ],
                ],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 12],
            ]),
        ]);

        $compatibility = HeimdalSdk::contracts()->imeis()->compatibility('12345678901234');

        $this->assertInstanceOf(ImeiCompatibilityDto::class, $compatibility);
        $this->assertTrue($compatibility->compatible);
        $this->assertTrue($compatibility->supportsEsim);
        $this->assertSame('supported', $compatibility->shortStatus);
    }

    public function test_contract_history_sends_query_and_returns_collection_dto(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/360/subscribers/525512345678/history/sim*' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-history',
                'operation' => 'v2.360.history.sim',
                'data' => ['records' => [['iccid' => '8952']]],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 18],
            ]),
        ]);

        $history = HeimdalSdk::contracts()->history()->sim(
            '525512345678',
            new DateRangeQuery('2026-05-01', '2026-05-13', limit: 500),
        );

        $this->assertInstanceOf(HistoryCollectionDto::class, $history);
        $this->assertSame('sim', $history->kind);
        $this->assertSame(1, $history->count);
        $this->assertSame('8952', $history->items[0]['iccid']);

        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://heimdal.example.test/api/v2/360/subscribers/525512345678/history/sim')
                && str_contains($request->url(), 'startDate=2026-05-01')
                && str_contains($request->url(), 'endDate=2026-05-13')
                && str_contains($request->url(), 'limit=500');
        });
    }

    public function test_contract_change_msisdn_extracts_new_msisdn(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/subscribers/525512345678' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-change',
                'operation' => 'v2.subscribers.update',
                'data' => ['changeSubscriberMSISDN' => ['newMsisdn' => '525500000000']],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 20],
            ]),
        ]);

        $result = HeimdalSdk::contracts()
            ->subscribers()
            ->changeMsisdn('525512345678', new ChangeMsisdnData('55', '1'));

        $this->assertSame('525512345678', $result->oldMsisdn);
        $this->assertSame('525500000000', $result->newMsisdn);
    }

    public function test_contract_activation_accepts_typed_payload_with_address(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/subscribers/525512345678/activate' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-activate',
                'operation' => 'v2.subscribers.activate',
                'data' => ['accepted' => true],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 24],
            ]),
        ]);

        $result = HeimdalSdk::contracts()
            ->subscribers()
            ->activate('525512345678', new SubscriberActivationData('OFFER-1', '19.4395546,-99.187162'));

        $this->assertInstanceOf(OperationResultDto::class, $result);
        $this->assertSame('v2.subscribers.activate', $result->operation);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://heimdal.example.test/api/v2/subscribers/525512345678/activate'
                && $request['offeringId'] === 'OFFER-1'
                && $request['address'] === '19.4395546,-99.187162';
        });
    }

    public function test_contract_msisdn_port_in_uses_v2_endpoint(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/msisdns/port-in-c' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-port-in',
                'operation' => 'v2.msisdns.port_in',
                'data' => ['accepted' => true],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 31],
            ]),
        ]);

        $result = HeimdalSdk::contracts()
            ->msisdns()
            ->portIn(new PortInData('525500000001', '525512345678', 'true'));

        $this->assertInstanceOf(OperationResultDto::class, $result);
        $this->assertSame('v2.msisdns.port_in', $result->operation);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://heimdal.example.test/api/v2/msisdns/port-in-c'
                && $request['msisdnTransitory'] === '525500000001'
                && $request['msisdnPorted'] === '525512345678'
                && $request['autoScriptReg'] === 'true';
        });
    }

    public function test_contract_batch_exposes_adapter_coverage_methods(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/batch/subscribers/changesSIM' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-batch',
                'operation' => 'v2.batch.subscribers.changes_sim',
                'data' => ['batch_id' => 'batch-1'],
                'meta' => ['provider' => 'altan', 'provider_status' => 202, 'duration_ms' => 40],
            ]),
        ]);

        $result = HeimdalSdk::contracts()
            ->batch()
            ->subscriberChangesSim([['msisdn' => '525512345678', 'newIccid' => '8952']]);

        $this->assertInstanceOf(BatchSubmissionDto::class, $result);
        $this->assertSame('batch-1', $result->get('batch_id'));

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://heimdal.example.test/api/v2/batch/subscribers/changesSIM'
                && $request['records'][0]['newIccid'] === '8952';
        });
    }

    public function test_altan_subscriber_not_found_is_typed_exception(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/subscribers/525512345678/profile' => Http::response([
                'success' => false,
                'correlation_id' => 'corr-missing',
                'operation' => 'v2.subscribers.profile',
                'error' => [
                    'type' => 'provider_error',
                    'code' => '1211000305',
                    'message' => 'The subscriber does not exist',
                    'provider' => ['errorCode' => '1211000305'],
                ],
                'meta' => ['provider' => 'altan', 'provider_status' => 404, 'duration_ms' => 10],
            ], 404),
        ]);

        $this->expectException(HeimdalSubscriberNotFoundException::class);

        HeimdalSdk::contracts()->subscribers()->profile('525512345678');
    }

    public function test_change_sim_factories_make_iccid_normalization_explicit(): void
    {
        $this->assertSame('895200000000000000', ChangeSimData::fromFullIccid('895200000000000000F')->newIccid);
        $this->assertSame('895200000000000000F', ChangeSimData::fromAltanIccid('895200000000000000F')->newIccid);
    }
}
