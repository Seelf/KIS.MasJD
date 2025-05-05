<?php

namespace App\Services;

use WebSocket\Client;
use Illuminate\Support\Facades\Log;
use App\Models\Measurement;

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
            $client = new Client($wsUri, ['timeout' => 3600]);
            echo "SubID:" . $subscriptionId . "\n";

            //STOMP CONNECT
            $connectFrame = "CONNECT\n" .
                "accept-version:1.2\n" .
                "host:pubsub.centersightcloud.com\n" .
                "\n\x00";
            $client->send($connectFrame);

            echo $response = $client->receive();
            if (!str_contains($response, 'CONNECTED')) {
                throw new \RuntimeException("Połączenie STOMP nieudane: {$response}");
            }

            //STOMP SUBSCRIBE
            $subscribeFrame = "SUBSCRIBE\n" .
                "id:{$subscriptionId}\n" .
                "destination:/topic/{$subscriptionId}\n" .
                "ack:auto\n" .
                "\n\x00";
            $client->send($subscribeFrame);

            while(true) {
                $response = $client->receive();

                // Parsuj STOMP frame
                $parts = explode("\n\n", $response, 2);
                if (count($parts) === 2) {
                    $body = rtrim($parts[1], "\x00\r\n");

                    $data = json_decode($body, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $this->handleMessage($data);
                    } else {
                        echo "Błąd jsona z websocket\n";
                        logger()->error('Niepoprawny JSON z WebSocket', ['raw' => $body]);
                    }
                } else {
                    echo "Błąd ramki stomp\n";
                    logger()->warning('Niepoprawna ramka STOMP', ['raw' => $response]);
                }

                usleep(100000); // 0.1 sekundy
            }
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

    protected function handleMessage(array $data): void
    {
        $info = $data['info'] ?? null;

        logger()->info('Dane do zapisu Measurement', [
            'data' => $data
        ]);

        if (!is_array($info) || !isset($info['key'])) {
            logger()->warning('Niepoprawny format wiadomości KIS', [
                'received_data' => $data
            ]);
            return;
        }

        try {
            Measurement::create([
                'node_id'           => $data['nodeId'] ?? 0,
                'key'               => $info['key'],
                'value'             => is_scalar($info['value']) ? (string) $info['value'] : json_encode($info['value']),
                'info_timestamp'    => $info['timestamp'] ?? now(),
                'message_timestamp' => $data['timestamp'] ?? now(),
                'message_type'      => $data['type'] ?? null,
                'json_type'         => $data['jsontype'] ?? null,
            ]);
        } catch (\Throwable $e) {
            logger()->error('Błąd podczas zapisu Measurement', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }
}
