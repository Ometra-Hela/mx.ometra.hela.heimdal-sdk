<?php

namespace Ometra\HeimdalSdk\Contracts\Mappers;

use Ometra\HeimdalSdk\Data\DateRangeQuery;
use Ometra\HeimdalSdk\Dtos\BatchSubmissionDto;
use Ometra\HeimdalSdk\Dtos\CoverageCheckResultDto;
use Ometra\HeimdalSdk\Dtos\DeviceInfoDto;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;
use Ometra\HeimdalSdk\Dtos\HistoryCollectionDto;
use Ometra\HeimdalSdk\Dtos\ImeiCompatibilityDto;
use Ometra\HeimdalSdk\Dtos\MonitoringHealthDto;
use Ometra\HeimdalSdk\Dtos\NumberValidationResultDto;
use Ometra\HeimdalSdk\Dtos\OperationResultDto;
use Ometra\HeimdalSdk\Dtos\SubscriberMsisdnChangeResultDto;
use Ometra\HeimdalSdk\Dtos\SubscriberProfileDto;
use Ometra\HeimdalSdk\Dtos\View360SearchResultDto;

class DomainResponseMapper
{
    public function operation(HeimdalResponseDto $response): OperationResultDto
    {
        return OperationResultDto::fromResponse($response);
    }

    public function batch(HeimdalResponseDto $response): BatchSubmissionDto
    {
        return new BatchSubmissionDto(
            attributes: $this->array($response->data),
            success: $response->success,
            operation: $response->operation,
            correlationId: $response->correlationId,
            providerStatus: $response->meta->providerStatus,
            raw: $response->data,
        );
    }

    public function profile(HeimdalResponseDto $response, string $msisdn): SubscriberProfileDto
    {
        $data = $this->array($response->data);
        $subscriber = $this->array($data['responseSubscriber'] ?? $data);
        $status = $subscriber['status'] ?? [];
        $primaryOffering = $this->array($subscriber['primaryOffering'] ?? []);
        $freeUnits = $this->list($subscriber['freeUnits'] ?? []);
        $detailOfferings = [];

        foreach ($freeUnits as $freeUnit) {
            foreach ($this->list($this->array($freeUnit)['detailOfferings'] ?? []) as $offering) {
                $detailOfferings[] = $offering;
            }
        }

        return new SubscriberProfileDto(
            attributes: $subscriber,
            msisdn: $this->string($subscriber['msisdn'] ?? $subscriber['MSISDN'] ?? $msisdn),
            status: $status,
            subStatus: $this->string($this->array($status)['subStatus'] ?? null),
            primaryOfferingId: $this->string($primaryOffering['offeringId'] ?? null),
            information: $subscriber['information'] ?? [],
            freeUnits: $freeUnits,
            detailOfferings: $detailOfferings,
            rawSubscriber: $subscriber,
            raw: $response->data,
        );
    }

    public function msisdnChange(HeimdalResponseDto $response, string $oldMsisdn): SubscriberMsisdnChangeResultDto
    {
        $data = $this->array($response->data);

        return new SubscriberMsisdnChangeResultDto(
            attributes: $data,
            success: $response->success,
            operation: $response->operation,
            correlationId: $response->correlationId,
            providerStatus: $response->meta->providerStatus,
            raw: $response->data,
            oldMsisdn: $oldMsisdn,
            newMsisdn: $this->string($this->find($data, ['newMsisdn', 'new_msisdn', 'msisdn'])),
        );
    }

    public function imeiCompatibility(HeimdalResponseDto $response): ImeiCompatibilityDto
    {
        $data = $this->array($response->data);
        $imeiPayload = $data['imei'] ?? $data['responseImei'] ?? null;
        $imei = is_array($imeiPayload) || is_object($imeiPayload) ? $this->array($imeiPayload) : $data;
        $deviceFeatures = $this->array($data['deviceFeatures'] ?? []);

        $blocked = $this->string($imei['blocked'] ?? null);
        $homologated = $this->string($imei['homologated'] ?? null);
        $band28 = $this->string($deviceFeatures['band28'] ?? null);
        $volteCapable = $this->string($deviceFeatures['volteCapable'] ?? null);
        $supportsEsim = strtoupper((string) ($imei['soportaESIM'] ?? $imei['supportsEsim'] ?? '')) === 'SI';

        [$compatible, $shortStatus] = $this->imeiCompatibilityState($imei, $deviceFeatures);

        return new ImeiCompatibilityDto(
            attributes: $data,
            imei: $this->string($imei['imei'] ?? $data['imei'] ?? null),
            blocked: $blocked,
            homologated: $homologated,
            band28: $band28,
            volteCapable: $volteCapable,
            supportsEsim: $supportsEsim,
            compatible: $compatible,
            shortStatus: $shortStatus,
            raw: $response->data,
        );
    }

    public function history(HeimdalResponseDto $response, string $kind, string $msisdn, DateRangeQuery $query): HistoryCollectionDto
    {
        $items = $this->historyItems($response->data);

        return new HistoryCollectionDto(
            attributes: ['items' => $items],
            kind: $kind,
            msisdn: $msisdn,
            items: $items,
            count: count($items),
            dateRange: $query->toArray(),
            raw: $response->data,
        );
    }

    public function view360Search(HeimdalResponseDto $response, string $identifierType, string $identifierValue): View360SearchResultDto
    {
        return new View360SearchResultDto(
            attributes: $this->array($response->data),
            identifierType: $identifierType,
            identifierValue: $identifierValue,
            data: $response->data,
            raw: $response->data,
        );
    }

    public function deviceInfo(HeimdalResponseDto $response, string $msisdn): DeviceInfoDto
    {
        return new DeviceInfoDto(
            attributes: $this->array($response->data),
            msisdn: $msisdn,
            data: $response->data,
            raw: $response->data,
        );
    }

    public function numberValidation(HeimdalResponseDto $response, string $msisdn, string $validationType): NumberValidationResultDto
    {
        return new NumberValidationResultDto(
            attributes: $this->array($response->data),
            msisdn: $msisdn,
            validationType: $validationType,
            data: $response->data,
            raw: $response->data,
        );
    }

    public function coverage(HeimdalResponseDto $response, string $latitude, string $longitude, string $technology): CoverageCheckResultDto
    {
        return new CoverageCheckResultDto(
            attributes: $this->array($response->data),
            latitude: $latitude,
            longitude: $longitude,
            technology: $technology,
            data: $response->data,
            raw: $response->data,
        );
    }

    public function monitoringHealth(HeimdalResponseDto $response): MonitoringHealthDto
    {
        $data = $this->array($response->data);

        return new MonitoringHealthDto(
            attributes: $data,
            state: $this->string($data['state'] ?? null),
            windowMinutes: $this->int($data['window_minutes'] ?? $data['windowMinutes'] ?? null),
            totalCalls: $this->int($data['total_calls'] ?? $data['totalCalls'] ?? null),
            errorCount: $this->int($data['error_count'] ?? $data['errorCount'] ?? null),
            errorRate: $this->float($data['error_rate'] ?? $data['errorRate'] ?? null),
            slowCount: $this->int($data['slow_count'] ?? $data['slowCount'] ?? null),
            raw: $response->data,
        );
    }

    /**
     * @return array{0: bool, 1: string}
     */
    private function imeiCompatibilityState(array $imei, array $deviceFeatures): array
    {
        if (strtoupper((string) ($imei['blocked'] ?? '')) !== 'NO') {
            return [false, 'blocked'];
        }

        if (strtoupper((string) ($imei['homologated'] ?? '')) === 'NO COMPATIBLE') {
            return [false, 'not_supported'];
        }

        if (strtoupper((string) ($imei['homologated'] ?? '')) === 'NO PROBADO') {
            return [false, 'not_tested'];
        }

        if (strtoupper((string) ($deviceFeatures['band28'] ?? '')) === 'NO') {
            return [false, 'not_supported'];
        }

        if ((string) ($imei['sub_category'] ?? '') === 'Solo Datos') {
            return [true, 'only_data'];
        }

        if (strtoupper((string) ($deviceFeatures['volteCapable'] ?? '')) === 'NO') {
            return [false, 'not_supported'];
        }

        if ((string) ($imei['imei'] ?? '') === 'Informacion no encontrada') {
            return [false, 'not_found'];
        }

        return [true, 'supported'];
    }

    /**
     * @return array<int, mixed>
     */
    private function historyItems(mixed $payload): array
    {
        $data = $this->array($payload);

        if ($data === [] && is_array($payload)) {
            return array_is_list($payload) ? $payload : [$payload];
        }

        if (array_is_list($data)) {
            return $data;
        }

        foreach (['data', 'history', 'items', 'records', 'results'] as $key) {
            if (array_key_exists($key, $data) && is_array($data[$key])) {
                return array_is_list($data[$key]) ? $data[$key] : [$data[$key]];
            }
        }

        return $data === [] ? [] : [$data];
    }

    /**
     * @param array<int, string> $keys
     */
    private function find(mixed $payload, array $keys): mixed
    {
        if (! is_array($payload)) {
            return null;
        }

        foreach ($payload as $key => $value) {
            if (is_string($key) && in_array($key, $keys, true) && is_scalar($value)) {
                return $value;
            }

            $nested = $this->find($value, $keys);
            if ($nested !== null) {
                return $nested;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function array(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return json_decode(json_encode($value), true) ?: [];
        }

        return [];
    }

    /**
     * @return array<int, mixed>
     */
    private function list(mixed $value): array
    {
        return is_array($value) && array_is_list($value) ? $value : [];
    }

    private function string(mixed $value): ?string
    {
        return is_scalar($value) && $value !== '' ? (string) $value : null;
    }

    private function int(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function float(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
