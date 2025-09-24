<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    /**
     * Get all locations with filters
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Location::query();

        // Apply filters
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                  ->orWhere('address', 'like', '%'.$filters['search'].'%');
            });
        }

        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Get location by ID
     */
    public function getById(int $id): Location
    {
        return Location::findOrFail($id);
    }

    /**
     * Create new location
     */
    public function create(array $data): Location
    {
        return Location::create($data);
    }

    /**
     * Update location
     */
    public function update(int $id, array $data): Location
    {
        $location = Location::findOrFail($id);
        $location->update($data);

        return $location;
    }

    /**
     * Delete location
     */
    public function delete(int $id): bool
    {
        $location = Location::findOrFail($id);

        // Check if location has events
        if ($location->events()->count() > 0) {
            throw new \Exception('لا يمكن حذف الموقع لأنه مرتبط بفعاليات');
        }

        return $location->delete();
    }
}
