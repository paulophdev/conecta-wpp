<?php

namespace App\Http\Controllers;

use App\Events\ConnectionStatusUpdated;
use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleSessionStatus(Request $request)
    {
        // Obtém os dados enviados pelo webhook
        $data = $request->all();

        // Verifica se o evento é "status-find"
        if (!$request->has('event') || $request->input('event') !== 'status-find') {
            return response()->json(['success' => true, 'message' => 'Webhook ignorado (evento não suportado)']);
        }

        Log::channel('webhook')->info('Webhook recebido:', $data);

        // Extrair o public_token (session) e status do payload
        $publicToken = $request->input('session');
        $status = $request->input('status');

        if (!$publicToken || !$status) {
            Log::channel('webhook')->warning('Webhook inválido: public_token ou status ausente.', $data);
            return response()->json(['success' => false, 'message' => 'Dados inválidos'], 400);
        }

        // Buscar a conexão pelo public_token
        $connection = Connection::where('public_token', $publicToken)->first();

        if (!$connection) {
            Log::channel('webhook')->warning('Conexão não encontrada para public_token: ' . $publicToken, $data);
            return response()->json(['success' => false, 'message' => 'Conexão não encontrada'], 404);
        }

        // Mapear o status para is_active
        $isActive = match ($status) {
            'isLogged', 'inChat' => true, // Consideramos conectado
            'notLogged', 'isNotConnected', 'browserClose', 'qrReadError' => false, // Consideramos desconectado
            default => $connection->is_active, // Mantém o estado atual se status desconhecido
        };

        // Atualizar o is_active no banco
        $connection->is_active = $isActive;
        $connection->save();

        // Disparar o evento WebSocket
        event(new ConnectionStatusUpdated($connection));

        return response()->json(['success' => true, 'message' => 'Webhook processado com sucesso']);
    }

    public function webhookDefault(Request $request)
    {
        return response()->json(['message' => 'Webhook recebido com sucesso.']);
    }
}