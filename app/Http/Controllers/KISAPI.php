<?php

namespace App\Http\Controllers;

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
        return view('kis.devices', compact('devices'));
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

        $url = "https://api.kisme.com/kisapi/v1/assets/" . $request->device_urn . "/setLed";

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'X-API-KEY'     => "440b837800e64923bae27c7e90ef9759",
            'X-CLIENT-ID'   => "ef61d0ef-198a-43ac-887b-fb1a305aa5a0"
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
