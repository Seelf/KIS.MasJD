<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KisMeService;

class KisMeController extends Controller
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
        return response()->json($devices);
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
