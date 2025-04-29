<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class KisMeService
{
    protected $apiUrl;
    protected $apiKey;
    protected $clientId;

    public function __construct()
    {
        $this->apiUrl = env('KIS_ME_API_URL');
        $this->apiKey = env('KIS_ME_API_KEY');
        $this->clientId = env('KIS_ME_CLIENT_ID');
    }


    public function testApiConnection()
    {
        $url = "{$this->apiUrl}/assets/urn:rafi:sbox:9c65f93cbf19/pressButton";

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
        /* TODO do poprawki branie danych z api przez env-a */

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'X-API-KEY'     => "440b837800e64923bae27c7e90ef9759", // Poprawiony nagłówek
            'X-CLIENT-ID'   => "ef61d0ef-198a-43ac-887b-fb1a305aa5a0",
        ])->get("https://api.kisme.com/kisapi/v1/assets"); // Upewniamy się, że URL jest zgodny z dokumentacją

        $data = $response->json() ?? []; // Zapewniamy, że zwracana jest tablica

        return $response->successful() ? $data : [];
    //        return env('KIS_ME_API_KEY');
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
