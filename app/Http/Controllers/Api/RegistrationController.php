<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {
    }

    /**
     * Get user's registrations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $registrations = $this->registrationService->getUserRegistrations(
                auth()->id(),
                $request->get('per_page', 15)//لتقسيم النتائج
            );

            return response()->json([
                'success' => true,
                'data' => $registrations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب التسجيلات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register for an event
     */
    public function register(Event $event): JsonResponse
    {
        try {
            $registration = $this->registrationService->register(auth()->id(), $event->id);

            return response()->json([
                'success' => true,
                'data' => $registration,
                'message' => 'تم التسجيل في الفعالية بنجاح',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Unregister from an event
     */
    public function unregister(Event $event): JsonResponse
    {
        try {
            $this->registrationService->unregister(auth()->id(), $event->id);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء التسجيل بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get event registrations (for organizers)
     */
    public function eventRegistrations(Event $event): JsonResponse
    {
        try {
            $registrations = $this->registrationService->getEventRegistrations($event->id);

            return response()->json([
                'success' => true,
                'data' => $registrations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب تسجيلات الفعالية',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
