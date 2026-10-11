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

    /**
     * Update service.
     */
    public function updateService(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'duration_minutes' => 'sometimes|integer|min:1',
            'category' => 'sometimes|string|max:255',
        ]);

        $service->update($validated);

        return response()->json([
            'message' => 'Service berhasil diperbarui',
            'service' => new ServiceResource($service->fresh()),
        ], 200);
    }

    public function destroyService(Service $service): JsonResponse
{
    $service->delete();

    return response()->json([
        'message' => 'Service berhasil dihapus'
    ]);
}


public function destroy(Invoice $invoice)
{
    $invoice->delete();

    return response()->json([
        'message' => 'Invoice berhasil dihapus'
    ], 200);
}
}

