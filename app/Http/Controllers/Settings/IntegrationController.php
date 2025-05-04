<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class IntegrationController extends Controller
{
    public function edit(Request $request)
    {
        $organization = $request->user()->organization;
        return Inertia::render('settings/Integration', [
            'api_key' => $organization?->api_key,
        ]);
    }

    public function generate(Request $request)
    {
        $organization = $request->user()->organization;
        
        if (!$organization) {
            $organization = new Organization();
            $organization->user_id = $request->user()->id;
        }

        $organization->api_key = Str::random(32);
        $organization->save();

        return response()->json([
            'api_key' => $organization->api_key,
            'success' => 'Chave de API gerada com sucesso!'
        ]);
    }
} 