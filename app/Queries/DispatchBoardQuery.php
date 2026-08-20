<?php

namespace App\Queries;

use App\Enums\TripStatus;
use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DispatchBoardQuery
{
    public function trips(
        string $search,
        ?TripStatus $status,
        ?int $driverId,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return Trip::query()
            ->select([
                'id',
                'customer_name',
                'pickup_address',
                'dropoff_address',
                'driver_id',
                'status',
                'estimated_fare',
                'version',
                'created_at',
            ])
            ->with([
                'driver:id,name,status',
            ])
            ->when(
                $search !== '',
                fn (Builder $query) => $this->applySearch($query, $search)
            )
            ->when(
                $status !== null,
                fn (Builder $query) => $query->where('status', $status->value)
            )
            ->when(
                $driverId !== null,
                fn (Builder $query) => $query->where('driver_id', $driverId)
            )
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Driver>
     */
    public function drivers(): Collection
    {
        return Driver::query()
            ->select([
                'id',
                'name',
                'status',
            ])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<string, int>
     */
    public function counts(): array
    {
        return Trip::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();
    }

    private function applySearch(
        Builder $query,
        string $search,
    ): void {
        $term = '%' . addcslashes($search, '%_') . '%';

        $query->where(function (Builder $query) use ($term): void {
            $query
                ->where('id', 'like', $term)
                ->orWhere('customer_name', 'like', $term)
                ->orWhere('pickup_address', 'like', $term)
                ->orWhere('dropoff_address', 'like', $term)
                ->orWhereHas(
                    'driver',
                    fn (Builder $driver) => $driver->where(
                        'name',
                        'like',
                        $term
                    )
                );
        });
    }
}