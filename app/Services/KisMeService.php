<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KisMeService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('KIS_ME_API_URL', 'http://api.kis.me');
        $this->apiKey = env('KIS_ME_API_KEY');
    }

    /**
     * Pobierz listę urządzeń
     */
    public function getDevices()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->get("{$this->apiUrl}/devices");

        return $response->json(); // Zwraca tablicę urządzeń
    }

    /**
     * Wywołaj trigger dla danego urządzenia
     */
    public function triggerDevice($triggerId, $deviceId, $params = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post("{$this->apiUrl}/triggers/{$triggerId}/activate", [
            'device_id' => $deviceId,
            'parameters' => $params
        ]);

        return $response->json(); // Zwraca odpowiedź API
    }
}