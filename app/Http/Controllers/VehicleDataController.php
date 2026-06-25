<?php

namespace App\Http\Controllers;

use App\Services\VehicleData;
use Illuminate\Http\JsonResponse;

class VehicleDataController extends Controller
{
    public function makes(): JsonResponse
    {
        return response()->json(VehicleData::makes());
    }

    public function models(string $make): JsonResponse
    {
        $models = VehicleData::models($make);

        if (empty($models)) {
            return response()->json([]);
        }

        return response()->json($models);
    }
}
