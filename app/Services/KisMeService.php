<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KisMeService
{
    protected $apiUrl;
    protected $apiKey;
    protected $clientId;

    public function __construct()
    {
        $this->apiUrl = env('KIS_ME_API_URL', 'http://api.kis.me');
        $this->apiKey = env('KIS_ME_API_KEY');
        $this->clientId = env('KIS_ME_CLIENT_ID');
    }


    public function testApiConnection()
    {
        $url = "{$this->apiUrl}/assets/urn:rafi:sbox:9c65f93cbaec/button2ColorKpi/datapointValues?limit=5";
    
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'X-CLIENT-ID' => $this->clientId,
            'Content-Type' => 'application/json'
        ])->get($url);
    
        if ($response->successful()) {
            \Log::info('✅ API działa poprawnie! Odpowiedź:', $response->json());
            return $response->json();
        } else {
            \Log::error("❌ Błąd API KIS.ME: {$response->status()}", ['response' => $response->body()]);
            return response()->json([
                'error' => 'Nie udało się połączyć z API KIS.ME',
                'status' => $response->status(),
                'response' => $response->body()
            ], $response->status());
        }
    }

    /**
     * Pobierz listę urządzeń
     */
    public function getDevices()
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->apiKey,
        'X-Client-ID'   => $this->clientId,
    ])->get("{$this->apiUrl}/devices");

    $data = $response->json() ?? []; // Upewniamy się, że nie zwróci null

    //\Log::info('KIS.ME API Response:', is_array($data) ? $data : ['error' => 'Invalid response format']);

    return $response->successful() ? $data : [];
}



    /**
     * Wywołaj trigger dla danego urządzenia
     */
    public function triggerDevice($triggerId, $deviceId, $params = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'X-Client-ID'   => $this->clientId,
            'Content-Type'  => 'application/json',
        ])->post("{$this->apiUrl}/triggers/{$triggerId}/activate", [
            'device_id' => $deviceId,
            'parameters' => $params
        ]);

        return $response->json();
    }
}