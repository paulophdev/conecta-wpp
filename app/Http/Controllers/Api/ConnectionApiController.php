<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Services\WppConnectService;

class ConnectionApiController extends Controller
{
    protected $wppConnectService;

    public function __construct(WppConnectService $wppConnectService)
    {
        $this->wppConnectService = $wppConnectService;
    }

    public function externalStatus($public_token, Request $request)
    {
        try {
            $organization = $this->getOrganizationFromRequest($request);
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organização não encontrada ou token inválido.'
                ], 401);
            }

            $connection = $organization->connections()->where('public_token', $public_token)->first();
            
            if (!$connection) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conexão não encontrada para esta organização.'
                ], 404);
            }

            // Checar status real na API WPPConnect
            $checkConnection = $this->wppConnectService->checkStatusConnection($public_token, $connection->private_token);
            $status = $checkConnection['status'] ? 'active' : 'inactive';

            return response()->json([
                'success' => true,
                'status' => $status
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro inesperado ao consultar status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendTextMessage($public_token, Request $request)
    {
        try {
            $organization = $this->getOrganizationFromRequest($request);
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organização não encontrada ou token inválido.'
                ], 401);
            }

            $connection = $organization->connections()->where('public_token', $public_token)->first();
            if (!$connection) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conexão não encontrada para esta organização.'
                ], 404);
            }

            $validated = $request->validate([
                'phone' => 'required|string|max:20',
                'message' => 'required|string|max:500',
                'isGroup' => 'nullable|boolean',
            ]);

            $isGroup = $validated['isGroup'] ?? false;

            $response = $this->wppConnectService->sendMessage(
                $connection->public_token,
                $connection->private_token,
                $validated['phone'],
                $validated['message'],
                $isGroup
            );

            if ($response['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Mensagem enviada com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao enviar mensagem.'
                ], 500);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro inesperado ao enviar mensagem.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendImageMessage($public_token, Request $request)
    {
        try {
            $organization = $this->getOrganizationFromRequest($request);
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organização não encontrada ou token inválido.'
                ], 401);
            }

            $connection = $organization->connections()->where('public_token', $public_token)->first();
            if (!$connection) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conexão não encontrada para esta organização.'
                ], 404);
            }

            $validated = $request->validate([
                'phone' => 'required|string|max:20',
                'image_url' => 'required|url',
                'caption' => 'nullable|string|max:500',
                'isGroup' => 'nullable|boolean',
            ]);

            $isGroup = $validated['isGroup'] ?? false;

            $response = $this->wppConnectService->sendImage(
                $connection->public_token,
                $connection->private_token,
                $validated['phone'],
                $validated['image_url'],
                $validated['caption'] ?? '',
                null,
                $isGroup
            );

            if ($response['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagem enviada com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao enviar imagem.'
                ], 500);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro inesperado ao enviar imagem.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getOrganizationFromRequest(Request $request): ?Organization
    {
        $authorization = $request->header('Authorization');
        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            return null;
        }
        $apiKey = trim(str_replace('Bearer', '', $authorization));
        return Organization::where('api_key', $apiKey)->first();
    }
} 