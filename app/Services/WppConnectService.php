<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class WppConnectService
{
    protected string $baseUrl;
    protected string $fixedKey;

    public function __construct()
    {
        $this->baseUrl = env('WPP_CONNECT_API_URL');
        $this->fixedKey = env('WPP_CONNECT_FIXED_KEY');
    }

    public function generateToken(string $uuid): array
    {
        $url = "{$this->baseUrl}/{$uuid}/{$this->fixedKey}/generate-token";

        try {
            $response = Http::post($url);

            if ($response->successful()) {
                return $response->json();
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            throw new \Exception("Erro ao gerar token na API WPP Connect: " . $e->getMessage());
        }
    }

    public function startSession(string $uuid, string $privateToken): array
    {
        $url = "{$this->baseUrl}/{$uuid}/start-session";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->post($url, [
                'session' => $uuid,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            throw new \Exception("Erro ao iniciar sessão na API WPP Connect: " . $e->getMessage());
        }
    }

    public function getStatus(string $publicToken, string $privateToken)
    {
        $url = "{$this->baseUrl}/{$publicToken}/status-session";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$privateToken}",
        ])->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Erro ao consultar status no WPP Connect: ' . $response->body());
    }

    public function checkStatusConnection(string $publicToken, string $privateToken)
    {
        $url = "{$this->baseUrl}/{$publicToken}/check-connection-session";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$privateToken}",
        ])->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Erro ao consultar status no WPP Connect: ' . $response->body());
    }
}