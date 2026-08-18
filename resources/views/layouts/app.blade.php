<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>{{ $title ?? 'ScanTech Dispatch' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body>
    <a href="#main-content" class="sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:not-sr-only focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-slate-950 focus:shadow-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
        Skip to main content
    </a>

    @auth
        <header class="sticky top-0 z-40 border-b border-white/10 bg-slate-950 text-white shadow-lg shadow-slate-950/5">
            <div class="mx-auto flex min-h-16 max-w-[1600px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-600 shadow-lg shadow-brand-950/25" aria-hidden="true">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="6" cy="19" r="2"></circle>
                            <circle cx="18" cy="5" r="2"></circle>
                            <path d="M8 19h3a4 4 0 0 0 4-4V9a4 4 0 0 1 3-3.87"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold tracking-wide">ScanTech Dispatch</p>
                        <p class="truncate text-xs text-slate-400">Operations workspace</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs capitalize text-slate-400">{{ auth()->user()->role }}</p>
                    </div>
                    <span class="hidden rounded-full border border-white/10 bg-white/10 px-2.5 py-1 text-xs font-semibold capitalize text-slate-200 md:inline-flex">
                        {{ auth()->user()->can_dispatch ? 'Dispatch enabled' : 'View only' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-ui.button type="submit" variant="dark" size="sm" aria-label="Log out">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M10 17l5-5-5-5"></path>
                                <path d="M15 12H3"></path>
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            </svg>
                            <span class="hidden sm:inline">Log out</span>
                        </x-ui.button>
                    </form>
                </div>
            </div>
        </header>
    @endauth

    {{ $slot ?? '' }}

    @livewireScripts
</body>
</html>
