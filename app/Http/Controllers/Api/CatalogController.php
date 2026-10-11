<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ServiceResource;
use App\Http\Resources\Api\TechnicianResource;
use App\Models\Service;
use App\Models\Technician;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * List all available service items.
     */
    public function services(Request $request): JsonResponse
    {
        $services = Service::orderBy('name', 'asc')->get();

        return response()->json([
            'services' => ServiceResource::collection($services),
        ], 200);
    }

    /**
     * List active/available technicians.
     */
    public function technicians(Request $request): JsonResponse
    {
        $technicians = Technician::with('ratings')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'technicians' => TechnicianResource::collection($technicians),
        ], 200);
    }
}
