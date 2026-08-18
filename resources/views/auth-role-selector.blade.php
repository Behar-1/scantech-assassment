<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>ScanTech Dispatch Assessment</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-950">
<a href="#main-content"
   class="sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:not-sr-only focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-slate-950 focus:shadow-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
    Skip to role selection
</a>

<main id="main-content" class="relative min-h-screen overflow-hidden bg-slate-950">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.22),transparent_38%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.12),transparent_34%)]"
         aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.04] [background-image:linear-gradient(rgba(255,255,255,.8)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.8)_1px,transparent_1px)] [background-size:48px_48px]"
         aria-hidden="true"></div>

    <div class="relative mx-auto flex min-h-screen w-full max-w-2xl flex-col justify-center px-4 py-10 sm:px-6 lg:px-8 lg:py-16">
        <section class="mb-8 text-white">
            <div class="flex items-center gap-3">
                <div class="grid size-12 place-items-center rounded-2xl bg-brand-600 shadow-xl shadow-blue-950/40"
                     aria-hidden="true">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="6" cy="19" r="2"></circle>
                        <circle cx="18" cy="5" r="2"></circle>
                        <path d="M8 19h3a4 4 0 0 0 4-4V9a4 4 0 0 1 3-3.87"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold tracking-wide">SCANTECH</p>
                    <p class="text-xs text-slate-400">Engineering assessment environment</p>
                </div>
            </div>
        </section>

        <section class="min-w-0 rounded-3xl border border-white/10 bg-white p-5 shadow-2xl shadow-slate-950/40 sm:p-8"
                 aria-labelledby="role-selector-heading">
            <div class="mb-6">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600">Local access</p>
                <h2 id="role-selector-heading"
                    class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Choose an assessment
                    role</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Each option creates a local-only authenticated session so authorization scenarios are easy to
                    reproduce.
                </p>
            </div>

            <div class="space-y-3">
                <form method="POST" action="{{ route('role.assume', 'dispatcher') }}">
                    @csrf
                    <button type="submit"
                            class="group flex min-h-20 w-full cursor-pointer items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-lg hover:shadow-brand-950/5 active:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
                            <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-blue-100"
                                  aria-hidden="true">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 11l9-7 9 7"></path>
                                    <path d="M5 10v10h14V10"></path>
                                    <path d="M9 20v-6h6v6"></path>
                                </svg>
                            </span>
                        <span class="min-w-0 flex-1">
                                <span class="block font-bold text-slate-900">Dispatcher</span>
                            </span>
                        <svg class="size-5 shrink-0 text-slate-400 transition group-hover:translate-x-1 group-hover:text-brand-600"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="M13 6l6 6-6 6"></path>
                        </svg>
                    </button>
                </form>

                <form method="POST" action="{{ route('role.assume', 'supervisor') }}">
                    @csrf
                    <button type="submit"
                            class="group flex min-h-20 w-full cursor-pointer items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-violet-300 hover:shadow-lg hover:shadow-violet-950/5 active:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2">
                            <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-violet-50 text-violet-700 ring-1 ring-violet-100"
                                  aria-hidden="true">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <path d="M9 12l2 2 4-4"></path>
                                </svg>
                            </span>
                        <span class="min-w-0 flex-1">
                                <span class="block font-bold text-slate-900">Supervisor</span>
                            </span>
                        <svg class="size-5 shrink-0 text-slate-400 transition group-hover:translate-x-1 group-hover:text-violet-600"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="M13 6l6 6-6 6"></path>
                        </svg>
                    </button>
                </form>

                <form method="POST" action="{{ route('role.assume', 'administrator') }}">
                    @csrf
                    <button type="submit"
                            class="group flex min-h-20 w-full cursor-pointer items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-lg hover:shadow-amber-950/5 active:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-amber-100"
                                  aria-hidden="true">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 21a8 8 0 0 1 16 0"></path>
                                    <path d="M18 8h4"></path>
                                </svg>
                            </span>
                        <span class="min-w-0 flex-1">
                                <span class="block font-bold text-slate-900">Administrator</span>
                            </span>
                        <svg class="size-5 shrink-0 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="M13 6l6 6-6 6"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </section>
    </div>
</main>
</body>
</html>
