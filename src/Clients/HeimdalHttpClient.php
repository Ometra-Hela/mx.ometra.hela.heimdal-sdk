<?php

namespace Ometra\HeimdalSdk\Clients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;
use Ometra\HeimdalSdk\Exceptions\MissingHeimdalConfigurationException;
use Ometra\HeimdalSdk\Support\ExceptionFactory;
use Ometra\HeimdalSdk\Support\ResponseFactory;

class HeimdalHttpClient
{
    private ?string $correlationId = null;

    private ?string $tokenOverride = null;

    private ?bool $throwOverride = null;

    private ResponseFactory $responses;

    private ExceptionFactory $exceptions;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(private readonly array $config)
    {
        $this->responses = new ResponseFactory();
        $this->exceptions = new ExceptionFactory();
    }

    /**
     * @return array<string, mixed>
     */
    public function config(): array
    {
        return $this->config;
    }

    public function baseUrl(): string
    {
        $baseUrl = $this->config['base_url'] ?? null;

        if (! is_string($baseUrl) || trim($baseUrl) === '') {
            throw MissingHeimdalConfigurationException::missingBaseUrl();
        }

        return rtrim($baseUrl, '/');
    }

    public function token(): ?string
    {
        $token = $this->tokenOverride ?? $this->config['token'] ?? null;

        return is_string($token) && $token !== '' ? $token : null;
    }

    public function withCorrelationId(string $correlationId): self
    {
        $clone = clone $this;
        $clone->correlationId = $correlationId;

        return $clone;
    }

    public function withToken(string $token): self
    {
        $clone = clone $this;
        $clone->tokenOverride = $token;

        return $clone;
    }

    public function withoutThrowing(): self
    {
        $clone = clone $this;
        $clone->throwOverride = false;

        return $clone;
    }

    public function throwing(): self
    {
        $clone = clone $this;
        $clone->throwOverride = true;

        return $clone;
    }

    /**
     * @param array<string, mixed> $query
     */
    public function get(string $uri, array $query = []): HeimdalResponseDto
    {
        return $this->handle($this->pending()->get($this->uri($uri), $query));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function post(string $uri, array $data = []): HeimdalResponseDto
    {
        return $this->handle($this->pending()->asJson()->post($this->uri($uri), $data));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function patch(string $uri, array $data = []): HeimdalResponseDto
    {
        return $this->handle($this->pending()->asJson()->patch($this->uri($uri), $data));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function delete(string $uri, array $data = []): HeimdalResponseDto
    {
        return $this->handle($this->pending()->asJson()->delete($this->uri($uri), $data));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function raw(string $method, string $uri, array $data = []): HeimdalResponseDto
    {
        $method = strtolower($method);

        return match ($method) {
            'get' => $this->get($uri, $data),
            'post' => $this->post($uri, $data),
            'patch' => $this->patch($uri, $data),
            'delete' => $this->delete($uri, $data),
            default => $this->handle($this->pending()->asJson()->send(strtoupper($method), $this->uri($uri), ['json' => $data])),
        };
    }

    /**
     * @param array<string, scalar|null> $fields
     */
    public function postMultipart(string $uri, string $field, mixed $contents, ?string $filename = null, array $fields = []): HeimdalResponseDto
    {
        $request = $this->pending()->asMultipart()->attach($field, $contents, $filename);

        foreach ($fields as $name => $value) {
            if ($value !== null) {
                $request = $request->attach($name, (string) $value);
            }
        }

        return $this->handle($request->post($this->uri($uri)));
    }

    private function pending(): PendingRequest
    {
        $request = Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->timeout($this->timeout())
            ->withHeader('X-Correlation-ID', $this->correlationId());

        $token = $this->token();
        if ($token !== null) {
            $request = $request->withToken($token);
        }

        $source = $this->sourceApp();
        if ($source !== null) {
            $request = $request->withHeader('X-Hela-App', $source);
        }

        $retry = $this->retry();
        if ($retry['times'] > 0) {
            $request = $request->retry($retry['times'], $retry['sleep']);
        }

        return $request;
    }

    private function handle(\Illuminate\Http\Client\Response $httpResponse): HeimdalResponseDto
    {
        $response = $this->responses->fromHttpResponse($httpResponse);

        if ($this->shouldThrow() && (! $response->success || ! $httpResponse->successful())) {
            throw $this->exceptions->make($response);
        }

        return $response;
    }

    private function uri(string $uri): string
    {
        return '/api/v2/' . ltrim($uri, '/');
    }

    private function correlationId(): string
    {
        if ($this->correlationId === null) {
            $this->correlationId = Str::uuid()->toString();
        }

        return $this->correlationId;
    }

    private function shouldThrow(): bool
    {
        if ($this->throwOverride !== null) {
            return $this->throwOverride;
        }

        return (bool) ($this->config['throw'] ?? true);
    }

    private function timeout(): int
    {
        return (int) ($this->config['timeout'] ?? 30);
    }

    /**
     * @return array{times: int, sleep: int}
     */
    private function retry(): array
    {
        $retry = is_array($this->config['retry'] ?? null) ? $this->config['retry'] : [];

        return [
            'times' => (int) ($retry['times'] ?? 0),
            'sleep' => (int) ($retry['sleep'] ?? 100),
        ];
    }

    private function sourceApp(): ?string
    {
        $source = $this->config['source'] ?? null;

        return is_string($source) && $source !== '' ? $source : null;
    }
}
