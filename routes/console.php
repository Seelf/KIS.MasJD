<?php

use App\Services\KisTextalkWebsocketService;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('kis:textalk-listen', function (KisTextalkWebsocketService $service) {
    $this->info('Łączenie z KIS WebSocket...');
    $service->connect();
})->describe('Połącz z WebSocket KIS przez textalk/websocket');
