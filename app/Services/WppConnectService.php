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

    public function sendMessage(string $publicToken, string $privateToken, string $phone, string $message, bool $isGroup = false): array
    {
        $url = "{$this->baseUrl}/{$publicToken}/send-message";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->post($url, [
                'phone' => $phone,
                'message' => $message,
                'isGroup' => $isGroup,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new RequestException($response);

        } catch (RequestException $e) {
            throw new \Exception("Erro ao enviar mensagem na API WPP Connect: " . $e->getMessage());
        }
    }

    public function sendImage(string $publicToken, string $privateToken, string $phone, string $imageUrl, string $caption = '', string $filename = null, bool $isGroup = false): array
    {
        $url = "{$this->baseUrl}/{$publicToken}/send-image";

        // Baixar a imagem e converter para base64
        $imageContents = @file_get_contents($imageUrl);
        if ($imageContents === false || strlen($imageContents) < 100) {
            throw new \Exception('Não foi possível baixar a imagem do link informado ou o arquivo está vazio.');
        }

        // Detectar o tipo MIME
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageContents) ?: 'image/jpeg';
        $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageContents);

        // Gerar filename aleatório se não enviado
        if (!$filename) {
            $ext = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'imagem_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->post($url, [
                'phone' => $phone,
                'isGroup' => $isGroup,
                'isNewsletter' => false,
                'isLid' => false,
                'filename' => $filename,
                'caption' => $caption,
                'base64' => $base64,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            throw new \Exception("Erro ao enviar imagem na API WPP Connect: " . $e->getMessage());
        }
    }

    public function sendImageBase64(string $publicToken, string $privateToken, string $phone, string $base64, string $caption = '', string $filename = null, bool $isGroup = false): array
    {
        $url = "{$this->baseUrl}/{$publicToken}/send-image";

        // Gerar filename aleatório se não enviado
        if (!$filename) {
            $filename = 'imagem_' . time() . '_' . rand(1000, 9999) . '.jpg';
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->post($url, [
                'phone' => $phone,
                'isGroup' => $isGroup,
                'isNewsletter' => false,
                'isLid' => false,
                'filename' => $filename,
                'caption' => $caption,
                'base64' => $base64,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            throw new \Exception("Erro ao enviar imagem na API WPP Connect: " . $e->getMessage());
        }
    }

    public function listGroups(string $publicToken, string $privateToken): array
    {
        $url = "{$this->baseUrl}/{$publicToken}/list-chats";

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$privateToken}",
            ])->post($url, [
                'onlyGroups' => true
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            throw new \Exception("Erro ao buscar grupos na API WPP Connect: " . $e->getMessage());
        }
    }
}