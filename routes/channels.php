<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('connection.{publicToken}', function ($user, $publicToken) {
    // Verifica se o usuário está autenticado
    return !is_null($user);
});