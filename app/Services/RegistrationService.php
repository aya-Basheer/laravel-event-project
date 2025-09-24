<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RegistrationService
{
    /**
     * Get user registrations
     */
    public function getUserRegistrations(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Registration::with(['event.location', 'event.speakers'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get event registrations
     */
    public function getEventRegistrations(int $eventId): Collection
    {
        return Registration::with('user')
            ->where('event_id', $eventId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Register user for event
     */
    public function register(int $userId, int $eventId): Registration
    {
        $event = Event::findOrFail($eventId);
        $user = User::findOrFail($userId);

        // Check if user is audience
        if (! $user->isAudience()) {
            throw new \Exception('التسجيل متاح للجمهور فقط');
        }

        // Check if event has passed
        if (Carbon::parse($event->starts_at)->isPast()) {
            throw new \Exception('لا يمكن التسجيل في فعالية انتهت');
        }

        // Check if already registered
        $existingRegistration = Registration::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->first();

        if ($existingRegistration) {
            throw new \Exception('أنت مسجل بالفعل في هذه الفعالية');
        }

        // Check capacity if set
        if ($event->capacity && $event->registrations()->count() >= $event->capacity) {
            throw new \Exception('الفعالية مكتملة العدد');
        }

        return Registration::create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'registered_at' => now(),
        ]);
    }

    /**
     * Unregister user from event
     */
    public function unregister(int $userId, int $eventId): bool
    {
        $registration = Registration::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->first();

        if (! $registration) {
            throw new \Exception('أنت غير مسجل في هذه الفعالية');
        }

        $event = Event::findOrFail($eventId);

        // Check if event has started (allow unregistration up to 1 hour before start)
        if (Carbon::parse($event->starts_at)->subHour()->isPast()) {
            throw new \Exception('لا يمكن إلغاء التسجيل قبل ساعة من بداية الفعالية');
        }

        return $registration->delete();
    }

    /**
     * Check if user is registered for event
     */
    public function isRegistered(int $userId, int $eventId): bool
    {
        return Registration::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->exists();
    }
}
