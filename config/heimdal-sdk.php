<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Source app
    |--------------------------------------------------------------------------
    |
    | Name of the HELA application consuming Heimdal. When present, the SDK sends
    | it as X-Hela-App so Heimdal and downstream observability can identify the
    | caller.
    |
    */
    'source' => env('HEIMDAL_SDK_SOURCE_APP'),

    /*
    |--------------------------------------------------------------------------
    | Heimdal API
    |--------------------------------------------------------------------------
    |
    | The base URL must point to the Heimdal host without the /api suffix. The SDK
    | appends /api/v2 for all typed and raw requests.
    |
    */
    'base_url' => env('HEIMDAL_SDK_BASE_URL'),
    'token' => env('HEIMDAL_SDK_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Response handling
    |--------------------------------------------------------------------------
    |
    | When enabled, Heimdal envelopes with success=false are converted into typed
    | exceptions. Consumers can override this per client using withoutThrowing().
    |
    */
    'throw' => filter_var(env('HEIMDAL_SDK_THROW', true), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | HTTP options
    |--------------------------------------------------------------------------
    */
    'timeout' => (int) env('HEIMDAL_SDK_TIMEOUT', 30),
    'retry' => [
        'times' => (int) env('HEIMDAL_SDK_RETRY_TIMES', 0),
        'sleep' => (int) env('HEIMDAL_SDK_RETRY_SLEEP', 100),
    ],
];
