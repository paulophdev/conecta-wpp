<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleSessionStatus(Request $request)
    {
        // Obtém os dados enviados pelo webhook
        $data = $request->all();

        // Salva os dados no log
        Log::channel('webhook')->info('Webhook recebido:', $data);

        // Retorna uma resposta para o webhook
        return response()->json(['message' => 'Webhook processado com sucesso']);
    }
}
