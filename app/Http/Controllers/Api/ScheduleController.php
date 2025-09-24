<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleCheckRequest;
use App\Services\ScheduleService;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService
    ) {
    }

    /**
     * Check for schedule conflicts
     */
    public function check(ScheduleCheckRequest $request): JsonResponse
    {
        try {
            $result = $this->scheduleService->checkConflicts($request->validated());

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في فحص الجدولة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
