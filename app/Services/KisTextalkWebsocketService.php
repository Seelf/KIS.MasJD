<?php

namespace App\Services;

use WebSocket\Client;
use Illuminate\Support\Facades\Log;

class KisTextalkWebsocketService
{
    protected string $serverUrl;
    protected string $apiKey;
    protected string $clientId;
    protected int $assetId;
    protected int $assetGroupId;

    public function __construct()
    {
        $this->serverUrl = config('kis.server_url');
        $this->apiKey = config('kis.api_key');
        $this->clientId = config('kis.client_id');
        $this->assetId = (int) config('kis.asset_id');
        $this->assetGroupId = (int) config('kis.asset_group_id');
    }

    public function connect(): void
    {
        $subscription = $this->createSubscription();
        $wsUri = str_replace("///", "//", $subscription['subscriptionUris'][0]);
        $subscriptionId = $subscription['subscriptionId'];

        try {
            $client = new Client($wsUri, ['timeout' => 60]);
            echo "SubID:" . $subscriptionId . "\n";

            // STOMP CONNECT
            $connectFrame = "CONNECT\n" .
                "accept-version:1.2\n" .
                "host:pubsub.centersightcloud.com\n" .
                "\n\x00";
            $client->send($connectFrame);

            echo $response = $client->receive();
            if (!str_contains($response, 'CONNECTED')) {
                throw new \RuntimeException("Połączenie STOMP nieudane: {$response}");
            }

//             STOMP SUBSCRIBE
            $subscribeFrame = "SUBSCRIBE\n" .
                "id:{$subscriptionId}\n" .
                "destination:/topic/{$subscriptionId}\n" .
                "ack:auto\n" .
                "\n\x00";
            $client->send($subscribeFrame);

            while(true) {
                echo $response = $client->receive();
                usleep(100000); // 0.1 sekundy
            }

//            echo "✅ Połączono i zasubskrybowano na /topic/{$subscriptionId}\n";
//
//            // Nasłuch loop
//            while (true) {
//                echo "Wykonuje \n" . " adres: " . $wsUri . "\n apikey: " . $this->apiKey . "\n clientid: " . $this->clientId . "\n AssetID:" . $this->assetId . "\n\n";
//                $response = $client->receive();
//                if ($response === false) {
//                    echo "Brak odpowiedzi od serwera WebSocket. \n";
//                    break;
//                }
//
//                $message = $this->parseStompMessage($response);
//                if ($message && isset($message['body'])) {
//                    echo $message['body'];
//                    $payload = json_decode($message['body'], true);
////                    Log::info('KIS websocket message', ['payload' => $payload]);
//                    echo "KIS websocket message received\n";
//                    $this->handleMessage($payload);
//                }
//
//                // Opóźnienie, aby nie obciążać CPU
//                usleep(500000); // 0.5 sekundy
//            }
        } catch (\Exception $e) {
            Log::error('Błąd połączenia z WebSocket', ['error' => $e->getMessage()]);
            echo "Błąd połączenia z WebSocket\n" . $e->getMessage() . " \n";
        }
    }

    protected function createSubscription(): array
    {
        $response = \Http::withHeaders([
            'X-API-KEY'     => $this->apiKey,
            'X-CLIENT-ID'   => $this->clientId,
            'Content-Type'  => 'application/json',
        ])->post($this->serverUrl, [
            'assetIds'       => [$this->assetId],
            'subscribeTo'    => 'datapoint',
        ]);

        if ($response->failed()) {
            dump('RESPONSE STATUS:', $response->status());
            dump('RESPONSE BODY:', $response->body());
            throw new \RuntimeException('Nie udało się pobrać subscriptionId');
        }

        return $response->json();
    }
}
