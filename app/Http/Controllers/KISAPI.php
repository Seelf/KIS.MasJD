<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KisMeService;

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
<<<<<<< HEAD
        return view('layouts.devices', ['devices' => $devices]);
=======
        return view('kis.devices', compact('devices'));
//        return response()->json($devices);
>>>>>>> c98dacacfff34e2d11b7f9e3248c8641f19e9de1
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
