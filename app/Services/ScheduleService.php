<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Location;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Check for schedule conflicts
     */
    public function checkConflicts(array $data): array
    {
        $locationId = $data['location_id'];
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = Carbon::parse($data['ends_at']);
        $excludeEventId = $data['event_id'] ?? null;

        $conflicts = [];
        $hasConflicts = false;

        // Check for time/location conflicts
        $timeConflicts = $this->checkTimeConflicts($locationId, $startsAt, $endsAt, $excludeEventId);
        if (! empty($timeConflicts)) {
            $conflicts = array_merge($conflicts, $timeConflicts);
            $hasConflicts = true;
        }

        // Check for speaker conflicts if speakers are provided
        if (isset($data['speaker_ids']) && ! empty($data['speaker_ids'])) {
            $speakerConflicts = $this->checkSpeakerConflicts($data['speaker_ids'], $startsAt, $endsAt, $excludeEventId);
            if (! empty($speakerConflicts)) {
                $conflicts = array_merge($conflicts, $speakerConflicts);
                $hasConflicts = true;
            }
        }

        $result = [
            'has_conflicts' => $hasConflicts,
            'conflicts' => $conflicts,
        ];

        // Add suggestions if there are conflicts
        if ($hasConflicts) {
            $result['suggestions'] = $this->generateSuggestions($locationId, $startsAt, $endsAt, $excludeEventId);
        }

        return $result;
    }

    /**
     * Check for time and location conflicts
     */
    private function checkTimeConflicts(int $locationId, Carbon $startsAt, Carbon $endsAt, ?int $excludeEventId = null): array
    {
        $query = Event::where('location_id', $locationId)
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->whereBetween('starts_at', [$startsAt, $endsAt])
                  ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                  ->orWhere(function ($q2) use ($startsAt, $endsAt) {
                      $q2->where('starts_at', '<=', $startsAt)
                         ->where('ends_at', '>=', $endsAt);
                  });
            });

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        $conflictingEvents = $query->with(['location', 'speakers'])->get();

        return $conflictingEvents->map(function ($event) {
            return [
                'id' => uniqid(),
                'event' => $event,
                'location' => $event->location,
                'conflict_type' => 'time_overlap',
                'severity' => $this->calculateSeverity($event),
                'conflicting_events' => [],
            ];
        })->toArray();
    }

    /**
     * Check for speaker conflicts
     */
    private function checkSpeakerConflicts(array $speakerIds, Carbon $startsAt, Carbon $endsAt, ?int $excludeEventId = null): array
    {
        $query = Event::whereHas('speakers', function ($q) use ($speakerIds) {
            $q->whereIn('speakers.id', $speakerIds);
        })
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->whereBetween('starts_at', [$startsAt, $endsAt])
                  ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                  ->orWhere(function ($q2) use ($startsAt, $endsAt) {
                      $q2->where('starts_at', '<=', $startsAt)
                         ->where('ends_at', '>=', $endsAt);
                  });
            });

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        $conflictingEvents = $query->with(['location', 'speakers'])->get();

        return $conflictingEvents->map(function ($event) {
            return [
                'id' => uniqid(),
                'event' => $event,
                'location' => $event->location,
                'conflict_type' => 'speaker_conflict',
                'severity' => 'high',
                'conflicting_events' => [],
            ];
        })->toArray();
    }

    /**
     * Calculate conflict severity
     */
    private function calculateSeverity(Event $event): string
    {
        $now = Carbon::now();
        $eventStart = Carbon::parse($event->starts_at);

        $hoursUntilEvent = $now->diffInHours($eventStart, false);

        if ($hoursUntilEvent < 24) {
            return 'high';
        } elseif ($hoursUntilEvent < 72) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Generate suggestions for alternative times/locations
     */
    private function generateSuggestions(int $locationId, Carbon $startsAt, Carbon $endsAt, ?int $excludeEventId = null): array
    {
        $suggestions = [
            'alternative_times' => $this->suggestAlternativeTimes($locationId, $startsAt, $endsAt, $excludeEventId),
            'alternative_locations' => $this->suggestAlternativeLocations($startsAt, $endsAt, $excludeEventId),
        ];

        return $suggestions;
    }

    /**
     * Suggest alternative times for the same location
     */
    private function suggestAlternativeTimes(int $locationId, Carbon $startsAt, Carbon $endsAt, ?int $excludeEventId = null): array
    {
        $duration = $startsAt->diffInHours($endsAt);
        $suggestions = [];

        // Check next 7 days
        for ($day = 0; $day < 7; $day++) {
            $checkDate = $startsAt->copy()->addDays($day);

            // Check different time slots
            $timeSlots = [
                ['09:00', '12:00'],
                ['14:00', '17:00'],
                ['19:00', '22:00'],
            ];

            foreach ($timeSlots as $slot) {
                $slotStart = $checkDate->copy()->setTimeFromTimeString($slot[0]);
                $slotEnd = $slotStart->copy()->addHours($duration);

                // Skip if it's the same time as requested
                if ($slotStart->eq($startsAt)) {
                    continue;
                }

                // Check if this slot is available
                $conflicts = $this->checkTimeConflicts($locationId, $slotStart, $slotEnd, $excludeEventId);

                if (empty($conflicts)) {
                    $suggestions[] = $slotStart->format('H:i').' - '.$slotEnd->format('H:i').' ('.$slotStart->format('Y-m-d').')';

                    if (count($suggestions) >= 3) {
                        break 2;
                    }
                }
            }
        }

        return $suggestions;
    }

    /**
     * Suggest alternative locations for the same time
     */
    private function suggestAlternativeLocations(Carbon $startsAt, Carbon $endsAt, ?int $excludeEventId = null): array
    {
        $locations = Location::all();
        $suggestions = [];

        foreach ($locations as $location) {
            $conflicts = $this->checkTimeConflicts($location->id, $startsAt, $endsAt, $excludeEventId);

            if (empty($conflicts)) {
                $suggestions[] = $location;

                if (count($suggestions) >= 3) {
                    break;
                }
            }
        }

        return $suggestions;
    }
}
