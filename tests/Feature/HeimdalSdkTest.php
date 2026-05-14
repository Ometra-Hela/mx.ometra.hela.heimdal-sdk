<?php

namespace Ometra\HeimdalSdk\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Ometra\HeimdalSdk\Data\ChangeSimData;
use Ometra\HeimdalSdk\Data\PurchaseProductData;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;
use Ometra\HeimdalSdk\Exceptions\HeimdalProviderException;
use Ometra\HeimdalSdk\Exceptions\HeimdalValidationException;
use Ometra\HeimdalSdk\Facades\HeimdalSdk as HeimdalSdkFacade;
use Ometra\HeimdalSdk\HeimdalSdk;
use Ometra\HeimdalSdk\Tests\TestCase;

class HeimdalSdkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('heimdal-sdk.base_url', 'https://heimdal.example.test/');
        $this->app['config']->set('heimdal-sdk.token', 'secret-token');
        $this->app['config']->set('heimdal-sdk.source', 'heimdal-sdk-tests');
        $this->app['config']->set('heimdal-sdk.throw', true);
    }

    public function test_it_registers_the_sdk_singleton_and_facade(): void
    {
        $this->assertInstanceOf(HeimdalSdk::class, $this->app->make(HeimdalSdk::class));
        $this->assertSame($this->app->make(HeimdalSdk::class), $this->app->make('heimdal-sdk'));
        $this->assertSame('https://heimdal.example.test', HeimdalSdkFacade::baseUrl());
        $this->assertSame('secret-token', HeimdalSdkFacade::token());
    }

    public function test_it_sends_bearer_token_source_and_generated_correlation_id(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/subscribers/525512345678/profile' => Http::response([
                'success' => true,
                'correlation_id' => 'provider-correlation',
                'operation' => 'v2.subscribers.profile',
                'data' => ['msisdn' => '525512345678'],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 10],
            ]),
        ]);

        $response = HeimdalSdkFacade::subscribers()->profile('525512345678');

        $this->assertInstanceOf(HeimdalResponseDto::class, $response);
        $this->assertTrue($response->success);
        $this->assertSame('provider-correlation', $response->correlationId);
        $this->assertSame('525512345678', $response->data['msisdn']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://heimdal.example.test/api/v2/subscribers/525512345678/profile'
                && $request->hasHeader('Authorization', 'Bearer secret-token')
                && $request->hasHeader('X-Hela-App', 'heimdal-sdk-tests')
                && $request->hasHeader('X-Correlation-ID');
        });
    }

    public function test_it_supports_correlation_and_token_overrides_with_typed_payloads(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/subscribers/525512345678' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-123',
                'operation' => 'v2.subscribers.update',
                'data' => ['accepted' => true],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 15],
            ]),
        ]);

        HeimdalSdkFacade::heimdal()
            ->withCorrelationId('corr-123')
            ->withToken('override-token')
            ->subscribers()
            ->changeSim('525512345678', new ChangeSimData('8952000000000000000'));

        Http::assertSent(function ($request) {
            return $request->url() === 'https://heimdal.example.test/api/v2/subscribers/525512345678'
                && $request->method() === 'PATCH'
                && $request->hasHeader('Authorization', 'Bearer override-token')
                && $request->hasHeader('X-Correlation-ID', 'corr-123')
                && $request->data() === [
                    'changeSubscriberSIM' => [
                        'newIccid' => '8952000000000000000',
                    ],
                ];
        });
    }

    public function test_provider_errors_throw_typed_exceptions(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/products/purchase' => Http::response([
                'success' => false,
                'correlation_id' => 'corr-provider',
                'operation' => 'v2.products.purchase',
                'error' => [
                    'type' => 'provider_error',
                    'code' => 'ALTAN-400',
                    'message' => 'Provider rejected request',
                    'detail' => null,
                    'provider' => ['errorCode' => 'ALTAN-400'],
                ],
                'meta' => ['provider' => 'altan', 'provider_status' => 400, 'duration_ms' => 20],
            ], 400),
        ]);

        try {
            HeimdalSdkFacade::products()->purchase(new PurchaseProductData('525512345678', ['OFFER-1']));
            $this->fail('Expected provider exception.');
        } catch (HeimdalProviderException $exception) {
            $this->assertSame(400, $exception->status);
            $this->assertSame('corr-provider', $exception->correlationId);
            $this->assertSame('v2.products.purchase', $exception->operation);
            $this->assertSame('ALTAN-400', $exception->errorCode);
        }
    }

    public function test_validation_errors_throw_typed_exceptions(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/products/remove' => Http::response([
                'success' => false,
                'correlation_id' => 'corr-validation',
                'operation' => 'v2.products.remove',
                'error' => [
                    'type' => 'validation_error',
                    'code' => '422',
                    'message' => 'The request payload is invalid.',
                    'detail' => ['offeringId' => ['required']],
                ],
                'meta' => ['provider' => 'altan', 'provider_status' => null, 'duration_ms' => 0],
            ], 422),
        ]);

        $this->expectException(HeimdalValidationException::class);

        HeimdalSdkFacade::heimdal()->post('products/remove', ['msisdn' => '525512345678']);
    }

    public function test_without_throwing_returns_error_response_dto(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/products/remove' => Http::response([
                'success' => false,
                'correlation_id' => 'corr-no-throw',
                'operation' => 'v2.products.remove',
                'error' => [
                    'type' => 'provider_error',
                    'code' => '400',
                    'message' => 'Provider error',
                ],
                'meta' => ['provider' => 'altan', 'provider_status' => 400, 'duration_ms' => 20],
            ], 400),
        ]);

        $response = HeimdalSdkFacade::heimdal()
            ->withoutThrowing()
            ->post('products/remove', ['msisdn' => '525512345678']);

        $this->assertFalse($response->success);
        $this->assertSame('provider_error', $response->error?->type);
        $this->assertSame('corr-no-throw', $response->correlationId);
    }

    public function test_batch_records_are_sent_as_json_records(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/batch/subscribers/suspends' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-batch',
                'operation' => 'v2.batch.subscribers.suspends',
                'data' => ['queued' => true],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 30],
            ]),
        ]);

        HeimdalSdkFacade::batch()->subscriberSuspends([
            ['msisdn' => '525512345678', 'scheduleDate' => '2026-05-13T10:00:00'],
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://heimdal.example.test/api/v2/batch/subscribers/suspends'
                && $request->data() === [
                    'records' => [
                        ['msisdn' => '525512345678', 'scheduleDate' => '2026-05-13T10:00:00'],
                    ],
                ];
        });
    }

    public function test_batch_csv_is_sent_as_multipart_csv_field(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/batch/subscribers/barrings' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-csv',
                'operation' => 'v2.batch.subscribers.barrings',
                'data' => ['queued' => true],
                'meta' => ['provider' => 'altan', 'provider_status' => 200, 'duration_ms' => 30],
            ]),
        ]);

        $path = tempnam(sys_get_temp_dir(), 'heimdal-sdk-');
        file_put_contents($path, "msisdn\n525512345678\n");

        try {
            HeimdalSdkFacade::batch()->subscriberBarrings($path);
        } finally {
            @unlink($path);
        }

        $recorded = Http::recorded();
        $this->assertCount(1, $recorded);

        $request = $recorded[0][0];
        $this->assertSame('https://heimdal.example.test/api/v2/batch/subscribers/barrings', $request->url());
        $this->assertSame('POST', $request->method());
        $this->assertTrue($request->isMultipart(), json_encode($request->headers()) ?: '');
        $this->assertTrue($request->hasFile('csv'), json_encode($request->data()) ?: '');
    }

    public function test_monitoring_filters_are_sent_as_query_parameters(): void
    {
        Http::fake([
            'https://heimdal.example.test/api/v2/monitoring/transactions*' => Http::response([
                'success' => true,
                'correlation_id' => 'corr-monitoring',
                'operation' => 'v2.monitoring.transactions',
                'data' => [],
                'meta' => ['provider' => 'heimdal', 'provider_status' => null, 'duration_ms' => null],
            ]),
        ]);

        HeimdalSdkFacade::monitoring()->transactions([
            'operation' => 'v2.subscribers.profile',
            'success' => false,
            'limit' => 25,
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://heimdal.example.test/api/v2/monitoring/transactions?operation=v2.subscribers.profile&success=0&limit=25';
        });
    }
}
