<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConnectionController extends Controller
{
    public function index()
    {
        $connections = Connection::all();
        return Inertia::render('Conections', [
            'connections' => $connections,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'webhook_url' => 'required|url|max:255',
            'public_token' => 'required|string|max:255',
        ]);

        Connection::create($validated);

        return redirect()->back()->with('success', 'Conexão criada com sucesso!');
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

    public function toggleWebhookStatus(Connection $connection)
    {
        $connection->update([
            'webhook_enable' => !$connection->webhook_enable
        ]);

        return response()->json(['success' => 'Conexão atualizada com sucesso!', 'webhook_enable' => $connection->webhook_enable]);
    }

    public function destroy(Connection $connection)
    {
        $connection->delete();

        return redirect()->back()->with('success', 'Conexão excluída com sucesso!');
    }
}