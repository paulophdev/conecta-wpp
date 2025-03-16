<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use App\Services\WppConnectService;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ConnectionController extends Controller
{
    protected $wppConnectService;

    public function __construct(WppConnectService $wppConnectService)
    {
        $this->wppConnectService = $wppConnectService;
    }

    public function index()
    {
        $connections = Connection::all();
        return Inertia::render('Connections', [
            'connections' => $connections,
        ]);
    }

    public function toggleWebhookStatus(Connection $connection)
    {
        $connection->update([
            'webhook_enable' => !$connection->webhook_enable
        ]);

        return response()->json(['success' => 'Conexão atualizada com sucesso!', 'webhook_enable' => $connection->webhook_enable]);
    }

    public function store(Request $request)
    {
        try {
            // Definindo mensagens de erro personalizadas em português
            $messages = [
                'name.required' => 'O nome é obrigatório.',
                'name.string' => 'O nome deve ser uma string.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
                'webhook_url.required' => 'A URL do webhook é obrigatória.',
                'webhook_url.url' => 'A URL do webhook deve ser uma URL válida.',
                'webhook_url.max' => 'A URL do webhook não pode ter mais de 255 caracteres.',
            ];

            // Validação com mensagens personalizadas
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'webhook_url' => 'required|url|max:255',
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $uuid = Uuid::uuid4()->toString(); // Gera um UUID v4 como token público

            // Criando a conexão com os dados validados e o token público
            $connection = Connection::create([
                'name' => $request->input('name'),
                'webhook_url' => $request->input('webhook_url'),
                'public_token' => $uuid,
            ]);

            // Gerar o token na API WPP Connect
            $response = $this->wppConnectService->generateToken($uuid);

            if ($response['status'] === 'success') {
                // Atualizar o private_token com o token retornado
                $connection->private_token = $response['token'];
                $connection->save();

                // Iniciar a sessão na API WPP Connect
                $this->wppConnectService->startSession(
                    $uuid,
                    $connection->private_token
                );
            } else {
                throw new \Exception('Resposta inválida da API WPP Connect ao gerar token');
            }

            // Retorno em JSON para o frontend
            return response()->json([
                'success' => true,
                'message' => 'Conexão criada e sessão iniciada com sucesso!',
                'data' => $connection->refresh()->toArray(),
            ], 201);

        } catch (\Throwable $th) {
            Log::error('Erro ao criar conexão: ' . $th->getMessage());

            // Em caso de erro, excluir a conexão criada
            if (isset($connection)) {
                $connection->delete();
            }

            return response()->json([
                'success' => false,
                'errors' => $th->getMessage(),
            ], 500);
        }
    }

    public function status(string $publicToken)
    {
        // Buscar a conexão pelo public_token
        $connection = Connection::where('public_token', $publicToken)->first();

        if (!$connection) {
            return response()->json([
                'success' => false,
                'message' => 'Conexão não encontrada.',
            ], 404); // Status HTTP 404 Not Found
        }

        try {
            // Iniciar a sessão na API WPP Connect
            $this->wppConnectService->startSession(
                $publicToken,
                $connection->private_token
            );

            // Consultar o status na API WPP Connect
            $statusData = $this->wppConnectService->getStatus($publicToken, $connection->private_token);

            // Retornar o status da conexão junto com informações básicas
            // Retornar o status da conexão junto com informações básicas
            return response()->json([
                'success' => true,
                'message' => 'Status da conexão obtido com sucesso!',
                'data' => [
                    'id' => $connection->id,
                    'name' => $connection->name,
                    'public_token' => $connection->public_token,
                    'status' => $statusData, // Dados retornados pela API WPP Connect
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar status no WPP Connect: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar o status da conexão.',
                'error' => $e->getMessage(),
            ], 500); // Status HTTP 500 Internal Server Error
        }
    }

    public function update(Request $request, Connection $connection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'webhook_url' => 'required|url|max:255',
            'public_token' => 'required|string|max:255',
        ]);

        $connection->update($validated);

        return redirect()->back()->with('success', 'Conexão atualizada com sucesso!');
    }

    public function destroy(Connection $connection)
    {
        $connection->delete();

        return redirect()->back()->with('success', 'Conexão excluída com sucesso!');
    }
}