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

    public function getProfileData(string $publicToken, string $privateToken): array
    {
        try {
            // 1. Obter o número de telefone
            $phoneUrl = "{$this->baseUrl}/{$publicToken}/get-phone-number";
            $phoneResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->get($phoneUrl);

            if (!$phoneResponse->successful()) {
                throw new \Exception('Erro ao obter número de telefone: ' . $phoneResponse->body());
            }

            $phoneData = $phoneResponse->json();
            $phoneNumber = $phoneData['response'] ?? null; // Ex.: "557592419101@c.us"
            $phone = str_replace('@c.us', '', $phoneNumber); // Remove "@c.us" se necessário

            // 2. Obter o nome do contato
            $contactUrl = "{$this->baseUrl}/{$publicToken}/contact/{$phone}";
            $contactResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->get($contactUrl);

            $name = null;
            if ($contactResponse->successful()) {
                $contactData = $contactResponse->json();
                $name = $contactData['response']['pushname'] ?? 'Desconhecido';
            }

            // 3. Obter a imagem do perfil
            $profilePicUrl = "{$this->baseUrl}/{$publicToken}/profile-pic/{$phone}";
            $profilePicResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->get($profilePicUrl);

            $profilePic = null;
            if ($profilePicResponse->successful()) {
                $profilePicData = $profilePicResponse->json();
                $profilePic = $profilePicData['response']['eurl'] ?? null;
            }

            return [
                'phone' => $phone,
                'name' => $name,
                'profile_picture' => $profilePic,
            ];
        } catch (\Exception $e) {
            // Retornar valores padrão em caso de erro
            return [
                'phone' => null,
                'name' => 'Desconhecido',
                'profile_picture' => null,
            ];
        }
    }

    public function closeSession(string $uuid, string $privateToken): array
    {
        $url = "{$this->baseUrl}/{$uuid}/close-session";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->post($url);

            if ($response->successful()) {
                return $response->json();
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            throw new \Exception("Erro ao iniciar sessão na API WPP Connect: " . $e->getMessage());
        }
    }

    public function logoutSession(string $uuid, string $privateToken): array
    {
        $url = "{$this->baseUrl}/{$uuid}/logout-session";

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
}