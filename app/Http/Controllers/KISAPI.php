<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use App\Services\KisMeService;
use Illuminate\Support\Facades\Http;

class KISAPI extends Controller
{
    protected $kisMeService;

    public function __construct(KisMeService $kisMeService)
    {
        $this->kisMeService = $kisMeService;
    }

    /**
     * Pobierz listę urządzeń
     */
    public function getDevices()
    {
        $devices = $this->kisMeService->getDevices();
        $measurements = Measurement::latest('info_timestamp')->take(20)->get();
        return view('kis.devices', compact('devices', 'measurements'));
        //return response()->json($devices);
    }

    /**
     * @throws ConnectionException
     */
    public function setLed(Request $request)
    {
        $request->validate([
            'device_urn' => 'required|string',
            'color'      => 'required|string',
            'flashing'   => 'required|string',
            'target'     => 'required|string',
        ]);

        $flashing = false;

        if($request->flashing == 'true') {
            $flashing = true;
        }

        $url = env('KIS_SERVER_URL_BASE') . '/assets/' . $request->device_urn . "/setLed";

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'X-API-KEY'     => env('KIS_API_KEY'),
            'X-CLIENT-ID'   => env('KIS_CLIENT_ID')
        ])->post($url, [
            'color'    => $request->color,
            'flashing' => $flashing,
            'target'   => $request->target,
        ]);

        return back()->with(compact('response'));
    }

    /**
     * Wywołaj trigger na urządzeniu
     */
    public function triggerDevice(Request $request, $triggerId)
    {
        $deviceId = $request->input('device_id');
        $params = $request->input('parameters', []);

        $result = $this->kisMeService->triggerDevice($triggerId, $deviceId, $params);

        return response()->json($result);
    }
}
