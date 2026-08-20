<?php

namespace App\Livewire;

use App\Enums\TripStatus;
use App\Exceptions\DriverUnavailableException;
use App\Exceptions\InvalidTripTransitionException;
use App\Exceptions\StaleTripException;
use App\Exceptions\TripNotAssignableException;
use App\Models\Driver;
use App\Models\Trip;
use App\Queries\DispatchBoardQuery;
use App\Services\Dispatch\AssignmentService;
use App\Services\Dispatch\TripFareService;
use App\Services\Dispatch\TripLifecycleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

#[Layout('layouts.app')]
class DispatchBoard extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public string $search = '';

    public string $status = '';

    public string $driverFilter = '';

    public int $perPage = 15;

    public ?int $selectedTripId = null;

    public ?int $targetDriverId = null;

    public ?float $selectedFare = null;

    public ?int $selectedVersion = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDriverFilter(): void
    {
        $this->resetPage();
    }

    public function selectTrip(int $tripId): void
    {
        $trip = Trip::query()->findOrFail($tripId);

        $this->authorize('view', $trip);

        $this->loadSelectedTripState($trip);
        $this->resetErrorBag();
    }

    public function assignDriver(
        int $tripId,
        AssignmentService $assignmentService,
    ): void {
        $this->validate([
            'targetDriverId' => [
                'required',
                'integer',
                'exists:drivers,id',
            ],
        ]);

        $trip = Trip::query()->findOrFail($tripId);

        $this->authorize('assign', $trip);

        $driver = Driver::query()->findOrFail($this->targetDriverId);

        try {
            $updatedTrip = $assignmentService->assign(
                trip: $trip,
                targetDriver: $driver,
                actor: auth()->user(),
                expectedVersion: $this->selectedVersion,
            );

            $this->loadSelectedTripState($updatedTrip);

            session()->flash(
                'success',
                'Driver assignment updated successfully.'
            );
        } catch (DriverUnavailableException|TripNotAssignableException $e) {
            $this->addError('assignment', $e->getMessage());
            $this->refreshSelected();
        } catch (StaleTripException $e) {
            $this->handleConflict($trip, $e);
        } catch (Throwable $e) {
            report($e);

            $this->addError(
                'assignment',
                'The assignment could not be completed. No changes were saved.'
            );
        }
    }

    public function cancelTrip(
        int $tripId,
        TripLifecycleService $lifecycleService,
    ): void {
        $trip = Trip::query()->findOrFail($tripId);

        $this->authorize('cancel', $trip);

        try {
            $updatedTrip = $lifecycleService->transition(
                trip: $trip,
                targetStatus: TripStatus::CANCELLED,
                actor: auth()->user(),
                expectedVersion: $this->selectedVersion,
                reason: 'Cancelled from dispatch board',
            );

            $this->loadSelectedTripState($updatedTrip);

            session()->flash('success', 'Trip cancelled.');
        } catch (InvalidTripTransitionException|StaleTripException $e) {
            if ($e instanceof StaleTripException) {
                $this->handleConflict($trip, $e);
                return;
            }

            $this->addError('status', $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            $this->addError(
                'status',
                'The trip could not be cancelled. No changes were saved.'
            );
        }
    }

    public function changeStatus(
        int $tripId,
        string $status,
        TripLifecycleService $lifecycleService,
    ): void {
        $trip = Trip::query()->findOrFail($tripId);

        $this->authorize('changeStatus', $trip);

        $targetStatus = TripStatus::tryFrom($status);

        if ($targetStatus === null) {
            $this->addError('status', 'Unknown trip status.');
            return;
        }

        try {
            $updatedTrip = $lifecycleService->transition(
                trip: $trip,
                targetStatus: $targetStatus,
                actor: auth()->user(),
                expectedVersion: $this->selectedVersion,
            );

            $this->loadSelectedTripState($updatedTrip);

            session()->flash('success', 'Trip status updated.');
        } catch (InvalidTripTransitionException $e) {
            $this->addError('status', $e->getMessage());
        } catch (StaleTripException $e) {
            $this->handleConflict($trip, $e);
        } catch (Throwable $e) {
            report($e);

            $this->addError(
                'status',
                'The status update could not be completed. No changes were saved.'
            );
        }
    }

    public function saveFare(
        int $tripId,
        TripFareService $fareService,
    ): void {
        $this->validate([
            'selectedFare' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $trip = Trip::query()->findOrFail($tripId);

        $this->authorize('updateFare', $trip);

        if ($this->selectedVersion === null) {
            $this->addError(
                'selectedFare',
                'The trip version is missing. Refresh the trip and try again.'
            );

            return;
        }

        try {
            $updatedTrip = $fareService->update(
                trip: $trip,
                fare: (float) $this->selectedFare,
                actor: auth()->user(),
                expectedVersion: $this->selectedVersion,
            );

            $this->loadSelectedTripState($updatedTrip);

            session()->flash(
                'success',
                'Estimated fare updated.'
            );
        } catch (StaleTripException $e) {
            $this->handleConflict($trip, $e);
        } catch (Throwable $e) {
            report($e);

            $this->addError(
                'selectedFare',
                'The fare could not be updated. No changes were saved.'
            );
        }
    }

    public function refreshSelected(): void
    {
        if (!$this->selectedTripId) {
            return;
        }

        $trip = Trip::query()
            ->with('driver:id,name,status')
            ->findOrFail($this->selectedTripId);

        $this->authorize('view', $trip);

        $this->loadSelectedTripState($trip);
        $this->resetErrorBag();
    }

    public function goToPage(int $page): void
    {
        $this->setPage(max(1, $page));
    }

    public function render(DispatchBoardQuery $boardQuery)
    {
        $status = $this->status !== ''
            ? TripStatus::tryFrom($this->status)
            : null;

        $driverId = $this->driverFilter !== ''
            ? (int) $this->driverFilter
            : null;

        $trips = $boardQuery->trips(
            search: trim($this->search),
            status: $status,
            driverId: $driverId,
            perPage: $this->perPage,
        );

        $selectedTrip = $this->selectedTripId
            ? Trip::query()
                ->with('driver:id,name,status')
                ->find($this->selectedTripId)
            : null;

        $selectedHistory = $selectedTrip
            ? $selectedTrip->statusHistory()
                ->with('actor:id,name')
                ->limit(10)
                ->get()
            : collect();

        $counts = $boardQuery->counts();

        return view('livewire.dispatch-board', [
            'trips' => $trips,
            'drivers' => $boardQuery->drivers(),
            'selectedTrip' => $selectedTrip,
            'selectedHistory' => $selectedHistory,
            'counts' => [
                'pending' => $counts[TripStatus::PENDING->value] ?? 0,
                'assigned' => $counts[TripStatus::ASSIGNED->value] ?? 0,
                'in_progress' => $counts[TripStatus::IN_PROGRESS->value] ?? 0,
                'completed' => $counts[TripStatus::COMPLETED->value] ?? 0,
            ],
        ]);
    }

    private function loadSelectedTripState(Trip $trip): void
    {
        $this->selectedTripId = $trip->id;
        $this->targetDriverId = $trip->driver_id;
        $this->selectedFare = (float) $trip->estimated_fare;
        $this->selectedVersion = $trip->version;
    }

    private function handleConflict(
        Trip $trip,
        StaleTripException $exception,
    ): void {
        $current = Trip::query()
            ->with('driver:id,name,status')
            ->findOrFail($trip->id);

        $this->loadSelectedTripState($current);

        $this->addError(
            'conflict',
            sprintf(
                'This trip changed while you were editing it (version %d → %d). The latest state has been loaded. Review it before trying again.',
                $exception->expectedVersion,
                $exception->currentVersion,
            )
        );
    }
}