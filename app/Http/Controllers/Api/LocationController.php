<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        private LocationService $locationService
    ) {
    }

    /**
     * Display a listing of locations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search']);
            $locations = $this->locationService->getAll($filters);

            return response()->json([
                'success' => true,
                'data' => $locations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب المواقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created location
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        try {
            $location = $this->locationService->create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $location,
                'message' => 'تم إضافة الموقع بنجاح',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إضافة الموقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified location
     */
    public function show(Location $location): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $location,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب تفاصيل الموقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified location
     */
    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        try {
            $location = $this->locationService->update($location->id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => $location,
                'message' => 'تم تحديث الموقع بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث الموقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified location
     */
    public function destroy(Location $location): JsonResponse
    {
        try {
            $this->locationService->delete($location->id);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الموقع بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في حذف الموقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
