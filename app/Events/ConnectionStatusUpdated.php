<?php

namespace App\Events;

use App\Models\Connection;
use App\Services\WppConnectService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ConnectionStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public $connectionData;

    public function __construct(Connection $connection)
    {
        $dados = (new WppConnectService())->getStatus(
            $connection->public_token,
            $connection->private_token
        );

        // Extrair apenas os dados necessários do objeto Connection
        $this->connectionData = [
            'id' => $connection->id,
            'name' => $connection->name,
            'public_token' => $connection->public_token,
            'is_active' => $connection->is_active,
            'status' => [
                'status' => $connection->is_active ? 'isLogged' : 'notLogged',
                'qrcode' => isset($dados['qrcode']) ? $dados['qrcode'] : null,
                'urlcode' => null,
                'version' => 'unknown',
                'session' => $connection->public_token,
            ]
        ];

        // Se o controller passou dados de status, use-os
        if (isset($connection->statusData) && is_array($connection->statusData)) {
            $this->connectionData['status'] = $connection->statusData;
        }
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('connection.' . $this->connectionData['public_token']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'status-updated';
    }

    public function broadcastWith(): array
    {
        return $this->connectionData;
    }
}