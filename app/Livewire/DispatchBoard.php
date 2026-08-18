<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripStatusHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DispatchBoard extends Component
{
    public string $search = '';
    public string $status = '';
    public string $driverFilter = '';
    public int $page = 1;
    public int $perPage = 15;

    public ?int $selectedTripId = null;
    public ?int $targetDriverId = null;
    public ?float $selectedFare = null;
    public ?int $selectedVersion = null;

    public function selectTrip(int $tripId): void
    {
        $trip = Trip::query()->findOrFail($tripId);

        $this->selectedTripId = $trip->id;
        $this->targetDriverId = $trip->driver_id;
        $this->selectedFare = $trip->estimated_fare;
        $this->selectedVersion = $trip->version;

        $this->resetErrorBag();
    }

    public function assignDriver(int $tripId): void
    {
        $validator = Validator::make(
            [
                'targetDriverId' => $this->targetDriverId,
            ],
            [
                'targetDriverId' => ['required', 'integer', 'exists:drivers,id'],
            ]);

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());
            return;
        }

        $trip = Trip::query()->findOrFail($tripId);
        $driver = Driver::query()->findOrFail($this->targetDriverId);

        if ($driver->status !== 'available') {
            $this->addError('assignment', 'The selected driver is no longer available.');
            return;
        }

        $previousTrip = $trip->only(['driver_id', 'status', 'version']);
        $oldDriverId = $trip->driver_id;

        // Makes the race easier to reproduce during the assessment.
        usleep(250000);

        $trip->driver_id = $driver->id;
        $trip->status = 'assigned';
        $trip->version = $trip->version + 1;
        $trip->save();

        $driver->status = 'assigned';
        $driver->save();

        ActivityLog::query()->create([
            'actor_id' => Auth::id(),
            'action' => $oldDriverId ? 'trip.reassigned' : 'trip.assigned',
            'entity_type' => Trip::class,
            'entity_id' => $trip->id,
            'previous_values' => $previousTrip,
            'new_values' => $trip->only(['driver_id', 'status', 'version']),
            'reason' => null,
            'created_at' => now(),
        ]);

        $this->selectedVersion = $trip->version;
        session()->flash('success', 'Driver assignment updated.');
    }

    public function cancelTrip(int $tripId): void
    {
        $trip = Trip::query()->findOrFail($tripId);
        $previous = $trip->only(['status', 'driver_id', 'version']);

        $trip->status = 'cancelled';
        $trip->version = $trip->version + 1;
        $trip->save();

        ActivityLog::query()->create([
            'actor_id' => Auth::id(),
            'action' => 'trip.cancelled',
            'entity_type' => Trip::class,
            'entity_id' => $trip->id,
            'previous_values' => $previous,
            'new_values' => $trip->only(['status', 'driver_id', 'version']),
            'reason' => 'Cancelled from dispatch board',
            'created_at' => now(),
        ]);

        $this->selectedVersion = $trip->version;
        session()->flash('success', 'Trip cancelled.');
    }

    public function changeStatus(int $tripId, string $status): void
    {
        $allowed = ['pending', 'assigned', 'driver_arriving', 'in_progress', 'completed', 'cancelled'];

        if (!in_array($status, $allowed, true)) {
            $this->addError('status', 'Unknown trip status.');
            return;
        }

        $trip = Trip::query()->findOrFail($tripId);
        $previousStatus = $trip->status;
        $previous = $trip->only(['status', 'driver_id', 'version']);

        $trip->status = $status;
        $trip->version = $trip->version + 1;
        $trip->save();

        if ($status === 'completed' && $trip->driver) {
            $trip->driver->status = 'available';
            $trip->driver->save();
        }

        TripStatusHistory::query()->create([
            'trip_id' => $trip->id,
            'previous_status' => $previousStatus,
            'new_status' => $status,
            'changed_by' => Auth::id(),
            'metadata' => ['source' => 'dispatch_board'],
            'created_at' => now(),
        ]);

        ActivityLog::query()->create([
            'actor_id' => Auth::id(),
            'action' => 'trip.status_changed',
            'entity_type' => Trip::class,
            'entity_id' => $trip->id,
            'previous_values' => $previous,
            'new_values' => $trip->only(['status', 'driver_id', 'version']),
            'reason' => null,
            'created_at' => now(),
        ]);

        $this->selectedVersion = $trip->version;
        session()->flash('success', 'Trip status updated.');
    }

    public function saveFare(int $tripId): void
    {
        $this->validate([
            'selectedFare' => ['required', 'numeric', 'min:0'],
        ]);

        $trip = Trip::query()->findOrFail($tripId);
        $previous = $trip->only(['estimated_fare', 'version']);

        $trip->estimated_fare = $this->selectedFare;
        $trip->version = $trip->version + 1;
        $trip->save();

        ActivityLog::query()->create([
            'actor_id' => Auth::id(),
            'action' => 'trip.fare_updated',
            'entity_type' => Trip::class,
            'entity_id' => $trip->id,
            'previous_values' => $previous,
            'new_values' => $trip->only(['estimated_fare', 'version']),
            'reason' => null,
            'created_at' => now(),
        ]);

        $this->selectedVersion = $trip->version;
        session()->flash('success', 'Estimated fare updated.');
    }

    public function refreshSelected(): void
    {
        if (!$this->selectedTripId) {
            return;
        }

        $trip = Trip::query()->findOrFail($this->selectedTripId);
        $this->targetDriverId = $trip->driver_id;
        $this->selectedFare = (float)$trip->estimated_fare;
        $this->selectedVersion = $trip->version;
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function render()
    {
        $query = Trip::query()
            ->when($this->search !== '', function ($query): void {
                $term = '%' . $this->search . '%';

                $query->where(function ($query) use ($term): void {
                    $query->where('id', 'like', $term)
                        ->orWhere('customer_name', 'like', $term)
                        ->orWhere('pickup_address', 'like', $term)
                        ->orWhere('dropoff_address', 'like', $term)
                        ->orWhereHas('driver', fn($driverQuery) => $driverQuery->where('name', 'like', $term));
                });
            })
            ->when($this->status !== '', fn($query) => $query->where('status', $this->status))
            ->when($this->driverFilter !== '', fn($query) => $query->where('driver_id', $this->driverFilter))
            ->latest('id');

        $allTrips = $query->get();
        $pageItems = $allTrips->forPage($this->page, $this->perPage)->values();
        $trips = new LengthAwarePaginator(
            $pageItems,
            $allTrips->count(),
            $this->perPage,
            $this->page,
            ['path' => request()->url()]
        );

        $selectedTrip = $this->selectedTripId
            ? Trip::query()->find($this->selectedTripId)
            : null;

        $selectedHistory = $selectedTrip
            ? $selectedTrip->statusHistory()->limit(10)->get()
            : collect();

        return view('livewire.dispatch-board', [
            'trips' => $trips,
            'drivers' => Driver::query()->orderBy('name')->get(),
            'selectedTrip' => $selectedTrip,
            'selectedHistory' => $selectedHistory,
            'counts' => [
                'pending' => Trip::query()->where('status', 'pending')->count(),
                'assigned' => Trip::query()->where('status', 'assigned')->count(),
                'in_progress' => Trip::query()->where('status', 'in_progress')->count(),
                'completed' => Trip::query()->where('status', 'completed')->count(),
            ],
        ]);
    }
}
