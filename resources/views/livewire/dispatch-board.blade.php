<main id="main-content" class="mx-auto w-full max-w-[1600px] overflow-x-hidden px-4 py-5 sm:px-6 sm:py-7 lg:px-8">
    <div
        class="pointer-events-none fixed inset-x-0 top-20 z-50 hidden justify-center"
        wire:loading.delay.flex
        role="status"
        aria-live="polite"
    >
        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-xl shadow-slate-950/10">
            <svg class="size-4 animate-spin text-brand-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"></circle>
                <path class="opacity-90" d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
            </svg>
            Updating dispatch board
        </div>
    </div>

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-brand-700">
                <span class="size-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                Live operations
            </div>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Dispatch command center</h1>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">
                Monitor the trip queue, inspect assignment state, and coordinate driver operations from one workspace.
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path>
            </svg>
            Updated {{ now()->format('H:i') }}
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm" role="status">
            <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 6L9 17l-5-5"></path>
            </svg>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->has('conflict'))
        <div
            class="mb-4 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 shadow-sm"
            role="alert"
        >
            <svg
                class="mt-0.5 size-5 shrink-0"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M12 3l9 18H3L12 3z"></path>
                <path d="M12 9v4"></path>
                <path d="M12 17h.01"></path>
            </svg>

            <div>
                <p class="font-semibold">This trip was changed elsewhere.</p>

                <p class="mt-1">
                    {{ $errors->first('conflict') }}
                </p>

                <p class="mt-1 text-xs text-amber-700">
                    The latest trip state has already been loaded.
                </p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm" role="alert">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path>
                </svg>
                <div>
                    <p class="font-semibold">The operation could not be completed.</p>
                    <ul class="mt-1 list-disc space-y-0.5 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <section class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Trip status totals">
        <x-ui.stat-card label="Pending" :value="number_format($counts['pending'])" tone="amber">
            <x-slot:icon>
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path>
                </svg>
            </x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card label="Assigned" :value="number_format($counts['assigned'])" tone="blue">
            <x-slot:icon>
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M16 11l2 2 4-4"></path>
                </svg>
            </x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card label="In progress" :value="number_format($counts['in_progress'])" tone="cyan">
            <x-slot:icon>
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 11l9-7 9 7"></path><path d="M5 10v10h14V10"></path><path d="M9 20v-6h6v6"></path>
                </svg>
            </x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card label="Completed" :value="number_format($counts['completed'])" tone="emerald">
            <x-slot:icon>
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
            </x-slot:icon>
        </x-ui.stat-card>
    </section>

    <div
        class="grid grid-cols-1 items-start gap-5 transition-opacity duration-200 xl:grid-cols-[minmax(0,1fr)_400px]"
        wire:loading.class="pointer-events-none opacity-60"
    >
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="trip-queue-heading">
            <div class="flex flex-col gap-1 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <h2 id="trip-queue-heading" class="text-base font-bold text-slate-950">Trip queue</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Search and filter the current dispatch workload.</p>
                </div>
                <span class="mt-2 inline-flex w-fit items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 sm:mt-0">
                    {{ number_format($trips->total()) }} results
                </span>
            </div>

            <div class="grid gap-3 border-b border-slate-200 bg-slate-50/70 p-4 md:grid-cols-2 xl:grid-cols-[minmax(240px,1.6fr)_minmax(160px,.8fr)_minmax(180px,.9fr)_auto] xl:p-5">
                <div>
                    <label for="trip-search" class="mb-1.5 block text-xs font-semibold text-slate-700">Search trips</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path>
                        </svg>
                        <x-ui.input
                            id="trip-search"
                            class="pl-10"
                            type="search"
                            placeholder="Trip, customer, route or driver"
                            wire:model.live.debounce.400ms="search"
                            autocomplete="off"
                        />
                    </div>
                </div>

                <div>
                    <label for="status-filter" class="mb-1.5 block text-xs font-semibold text-slate-700">Trip status</label>
                    <x-ui.select id="status-filter" wire:model.live="status">
                        <option value="">All statuses</option>

                        @foreach (\App\Enums\TripStatus::cases() as $tripStatus)
                            <option value="{{ $tripStatus->value }}">
                                {{ $tripStatus->label() }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </div>

                <div>
                    <label for="driver-filter" class="mb-1.5 block text-xs font-semibold text-slate-700">Assigned driver</label>
                   <x-ui.select id="driver-filter" wire:model.live="driverFilter">
                        <option value="">All drivers</option>

                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}">
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </div>

                <div class="flex items-end">
                    <x-ui.button class="w-full xl:w-auto" wire:click="$refresh" wire:loading.attr="disabled">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 6v5h-5"></path><path d="M4 18v-5h5"></path><path d="M18.5 9A7 7 0 0 0 6 6.5L4 11"></path><path d="M5.5 15A7 7 0 0 0 18 17.5l2-4.5"></path>
                        </svg>
                        Refresh
                    </x-ui.button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[860px] w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-200 bg-white">
                            <th scope="col" class="px-5 py-3 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500">Trip</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500">Customer and route</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500">Driver</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500">Status</th>
                            <th scope="col" class="px-4 py-3 text-right text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500">Fare</th>
                            <th scope="col" class="px-5 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($trips as $trip)
                            <tr
                                wire:key="trip-{{ $trip->id }}"
                                @class([
                                    'group transition-colors hover:bg-slate-50/80',
                                    'bg-brand-50/70' => $selectedTripId === $trip->id,
                                ])
                            >
                                <td class="whitespace-nowrap px-5 py-4 align-top">
                                    <p class="font-bold text-slate-950">#{{ $trip->id }}</p>
                                    <p class="mt-1 font-mono text-[11px] text-slate-400">VERSION {{ $trip->version }}</p>
                                </td>
                                <td class="max-w-md px-4 py-4 align-top">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $trip->customer_name }}</p>
                                    <div class="mt-2 grid grid-cols-[12px_minmax(0,1fr)] gap-x-2 gap-y-1 text-xs text-slate-500">
                                        <span class="mt-1 size-2 rounded-full border-2 border-emerald-500 bg-white" aria-hidden="true"></span>
                                        <span class="truncate">{{ $trip->pickup_address }}</span>
                                        <span class="mt-1 size-2 rounded-sm bg-brand-500" aria-hidden="true"></span>
                                        <span class="truncate">{{ $trip->dropoff_address }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    @if ($trip->driver)
                                        <div class="flex items-center gap-2">
                                            <span class="grid size-7 shrink-0 place-items-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-600" aria-hidden="true">
                                                {{ str($trip->driver->name)->substr(0, 1)->upper() }}
                                            </span>
                                            <span class="max-w-40 truncate">{{ $trip->driver->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <x-ui.status-badge :status="$trip->status" />
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-right align-top text-sm font-bold text-slate-900">
                                    &euro;{{ number_format((float) $trip->estimated_fare, 2) }}
                                </td>
                                <td class="px-5 py-4 text-right align-top">
                                    <x-ui.button
                                        size="sm"
                                        wire:click="selectTrip({{ $trip->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="selectTrip({{ $trip->id }})"
                                        aria-label="Open trip {{ $trip->id }}"
                                    >
                                        Open
                                        <svg class="size-3.5 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M9 18l6-6-6-6"></path>
                                        </svg>
                                    </x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="mx-auto grid size-12 place-items-center rounded-2xl bg-slate-100 text-slate-400" aria-hidden="true">
                                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path>
                                        </svg>
                                    </div>
                                    <p class="mt-4 font-semibold text-slate-800">No trips found</p>
                                    <p class="mt-1 text-sm text-slate-500">Adjust the search term or filters and try again.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50/70 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p class="text-xs text-slate-500">
                    Page <span class="font-semibold text-slate-700">{{ $trips->currentPage() }}</span> of <span class="font-semibold text-slate-700">{{ max(1, $trips->lastPage()) }}</span>
                    <span class="mx-1 text-slate-300">&middot;</span>
                    {{ number_format($trips->total()) }} matching trips
                </p>
                <div class="flex gap-2">
                    <x-ui.button
                        size="sm"
                        wire:click="goToPage({{ max(1, $trips->currentPage() - 1) }})"
                        wire:loading.attr="disabled"
                        :disabled="$trips->onFirstPage()"
                    >
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
                        Previous
                    </x-ui.button>
                    <x-ui.button
                        size="sm"
                        wire:click="goToPage({{ min(max(1, $trips->lastPage()), $trips->currentPage() + 1) }})"
                        wire:loading.attr="disabled"
                        :disabled="!$trips->hasMorePages()"
                    >
                        Next
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>
                    </x-ui.button>
                </div>
            </div>
        </section>

        <aside class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:sticky xl:top-24" aria-label="Trip details">
            @if ($selectedTrip)
                <div class="border-b border-slate-200 bg-slate-950 px-5 py-5 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Selected trip</p>
                            <div class="mt-1.5 flex items-center gap-2">
                                <h2 class="text-xl font-bold">Trip #{{ $selectedTrip->id }}</h2>
                                <x-ui.status-badge :status="$selectedTrip->status" />
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Loaded version {{ $selectedVersion }}</p>
                        </div>
                        <x-ui.button
                            variant="dark"
                            size="sm"
                            wire:click="refreshSelected"
                            wire:loading.attr="disabled"
                            wire:target="refreshSelected"
                            aria-label="Refresh selected trip"
                        >
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 6v5h-5"></path><path d="M4 18v-5h5"></path><path d="M18.5 9A7 7 0 0 0 6 6.5L4 11"></path><path d="M5.5 15A7 7 0 0 0 18 17.5l2-4.5"></path>
                            </svg>
                        </x-ui.button>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    <section class="p-5" aria-labelledby="trip-overview-heading">
                        <h3 id="trip-overview-heading" class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Trip overview</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Customer</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedTrip->customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Estimated fare</p>
                                <p class="mt-1 text-sm font-bold text-slate-900">&euro;{{ number_format((float) $selectedTrip->estimated_fare, 2) }}</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl bg-slate-50 p-3.5">
                            <div class="grid grid-cols-[14px_minmax(0,1fr)] gap-x-2 gap-y-3 text-xs">
                                <span class="mt-1 size-2.5 rounded-full border-2 border-emerald-500 bg-white" aria-hidden="true"></span>
                                <div><p class="font-semibold text-slate-500">Pickup</p><p class="mt-0.5 leading-5 text-slate-800">{{ $selectedTrip->pickup_address }}</p></div>
                                <span class="mt-1 size-2.5 rounded-sm bg-brand-500" aria-hidden="true"></span>
                                <div><p class="font-semibold text-slate-500">Drop-off</p><p class="mt-0.5 leading-5 text-slate-800">{{ $selectedTrip->dropoff_address }}</p></div>
                            </div>
                        </div>
                    </section>

                    <section class="p-5" aria-labelledby="driver-assignment-heading">
                        <h3 id="driver-assignment-heading" class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Driver assignment</h3>
                        <label for="target-driver" class="mt-4 mb-1.5 block text-xs font-semibold text-slate-700">Selected driver</label>
                        <x-ui.select id="target-driver" wire:model="targetDriverId">
                            <option value="">Choose driver</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">
                                    {{ $driver->name }} &middot; {{ str($driver->status->value)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </x-ui.select>

                       @if (auth()->user()->canDispatchTrips()
                        && (!$selectedTrip->driver_id || auth()->user()->isSupervisor()))
                            <x-ui.button
                                class="mt-3 w-full"
                                variant="primary"
                                wire:click="assignDriver({{ $selectedTrip->id }})"
                                wire:loading.attr="disabled"
                                wire:target="assignDriver({{ $selectedTrip->id }})"
                            >
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M19 8v6"></path><path d="M16 11h6"></path>
                                </svg>
                                {{ $selectedTrip->driver_id ? 'Reassign driver' : 'Assign driver' }}
                            </x-ui.button>
                        @endif
                    </section>

                    <section class="p-5" aria-labelledby="fare-heading">
                        <h3 id="fare-heading" class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Estimated fare</h3>
                        <label for="selected-fare" class="mt-4 mb-1.5 block text-xs font-semibold text-slate-700">Fare amount in euros</label>
                        <div class="flex gap-2">
                            <div class="relative min-w-0 flex-1">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">&euro;</span>
                                <x-ui.input id="selected-fare" class="pl-8" type="number" min="0" step="0.01" wire:model="selectedFare" />
                            </div>
                            @if (auth()->user()->canDispatchTrips())
                             <x-ui.button
                                wire:click="saveFare({{ $selectedTrip->id }})"
                                wire:loading.attr="disabled"
                                wire:target="saveFare({{ $selectedTrip->id }})"
                                >
                                    Save
                                </x-ui.button>
                            @endif
                        </div>
                    </section>

                   @if (auth()->user()->canDispatchTrips())
                        <section class="p-5" aria-labelledby="status-actions-heading">
                            <h3 id="status-actions-heading" class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Status actions</h3>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <x-ui.button size="sm" wire:click="changeStatus({{ $selectedTrip->id }}, 'assigned')" wire:loading.attr="disabled">Assigned</x-ui.button>
                                <x-ui.button size="sm" wire:click="changeStatus({{ $selectedTrip->id }}, 'driver_arriving')" wire:loading.attr="disabled">Driver arriving</x-ui.button>
                                <x-ui.button size="sm" wire:click="changeStatus({{ $selectedTrip->id }}, 'in_progress')" wire:loading.attr="disabled">In progress</x-ui.button>
                                <x-ui.button size="sm" wire:click="changeStatus({{ $selectedTrip->id }}, 'completed')" wire:loading.attr="disabled">Completed</x-ui.button>
                            </div>
                        </section>
                    @endif

                    @if (auth()->user()->isSupervisor())
                        <section class="p-5" aria-labelledby="supervisor-actions-heading">
                            <h3 id="supervisor-actions-heading" class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Supervisor actions</h3>
                            <p class="mt-2 text-xs leading-5 text-slate-500">Cancellation is a terminal action and should be used deliberately.</p>
                            <x-ui.button
                                class="mt-3 w-full"
                                variant="danger"
                                wire:click="cancelTrip({{ $selectedTrip->id }})"
                                wire:loading.attr="disabled"
                                wire:target="cancelTrip({{ $selectedTrip->id }})"
                            >
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle><path d="M9 9l6 6"></path><path d="M15 9l-6 6"></path>
                                </svg>
                                Cancel trip
                            </x-ui.button>
                        </section>
                    @endif

                    <section class="p-5" aria-labelledby="status-history-heading">
                        <h3 id="status-history-heading" class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Recent status history</h3>
                        <ol class="mt-4 space-y-0">
                            @forelse ($selectedHistory as $history)
                               <li class="group relative grid grid-cols-[18px_minmax(0,1fr)] gap-3 pb-4 last:pb-0">
                                    <span
                                        class="absolute left-[8px] top-4 h-full w-px bg-slate-200 group-last:hidden"
                                        aria-hidden="true"
                                    ></span>

                                    <span
                                        class="relative mt-1 size-4 rounded-full border-4 border-white bg-brand-500 ring-1 ring-brand-200"
                                        aria-hidden="true"
                                    ></span>

                                    <div>
                                        <p class="text-xs font-semibold text-slate-800">
                                            {{ str($history->previous_status ?? 'No status')->replace('_', ' ')->title() }}

                                            <span class="mx-1 text-slate-300">&rarr;</span>

                                            {{ str($history->new_status)->replace('_', ' ')->title() }}
                                        </p>

                                        <p class="mt-1 text-[11px] text-slate-400">
                                            {{ $history->actor?->name ?? 'Unknown actor' }}
                                            &middot;
                                            {{ optional($history->created_at)->format('Y-m-d H:i:s') }}
                                        </p>
                                    </div>
                                </li>
                            @empty
                                <li class="rounded-xl bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">No status history recorded.</li>
                            @endforelse
                        </ol>
                    </section>
                </div>
            @else
                <div class="grid min-h-[430px] place-items-center p-8 text-center">
                    <div class="max-w-xs">
                        <div class="mx-auto grid size-16 place-items-center rounded-2xl bg-brand-50 text-brand-600 ring-1 ring-brand-100" aria-hidden="true">
                            <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 11l9-7 9 7"></path><path d="M5 10v10h14V10"></path><path d="M9 20v-6h6v6"></path>
                            </svg>
                        </div>
                        <h2 class="mt-5 text-lg font-bold text-slate-900">Select a trip to begin</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Open a trip from the queue to review its route, assignment, fare, and status history.</p>
                    </div>
                </div>
            @endif
        </aside>
    </div>
</main>
