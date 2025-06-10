<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class KisMeService
{
    protected string $serverUrl;
    protected string $apiKey;
    protected string $urn;
    protected string $clientId;
    protected int $assetId;
    protected int $assetGroupId;

    public function __construct()
    {
        $this->baseUrl = config('kis.base_url');
        $this->apiKey = config('kis.api_key');
        $this->urn = config('kis.urn');
        $this->clientId = config('kis.client_id');
        $this->assetId = (int) config('kis.asset_id');
        $this->assetGroupId = (int) config('kis.asset_group_id');
    }


    public function testApiConnection()
    {
        $url = "{$this->baseUrl}/assets/{$this->urn}/pressButton";

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
            'X-API-KEY'     => $this->apiKey, // Poprawiony nagłówek
            'X-CLIENT-ID'   => $this->clientId,
        ])->get("{$this->baseUrl}/assets"); // Upewniamy się, że URL jest zgodny z dokumentacją

        $data = $response->json() ?? []; // Zapewniamy, że zwracana jest tablica

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
        ])->post("{$this->baseUrl}/triggers/{$triggerId}/activate", [
            'device_id' => $deviceId,
            'parameters' => $params
        ]);

        return $response->json();
    }

    public function getLedStatus(string $deviceUrn): ?array
{
    $url = "https://api.kisme.com/kisapi/v1/assets/{$deviceUrn}";

    $response = Http::withHeaders([
        'Accept'        => 'application/json',
        'X-API-KEY'     => "440b837800e64923bae27c7e90ef9759",
        'X-CLIENT-ID'   => "ef61d0ef-198a-43ac-887b-fb1a305aa5a0",
    ])->get($url);

    // Debug pełnej odpowiedzi, nie tylko JSON
    logger()->info('KISME STATUS RESPONSE', [
        'url' => $url,
        'http_code' => $response->status(),
        'response' => $response->body(),
    ]);

    if (!$response->successful()) {
        return null;
    }

    return $response->json();
}
}
