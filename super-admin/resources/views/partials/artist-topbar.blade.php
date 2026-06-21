<header class="h-24 px-gutter md:px-lg flex items-center justify-between border-b border-outline-variant/20 bg-background/70 backdrop-blur-md sticky top-0 z-30">
    <div class="flex items-center gap-sm min-w-0">
        <button id="sidebar-toggle"
                class="lg:hidden w-10 h-10 rounded-lg bg-surface-container hover:bg-surface-container-high flex items-center justify-center"
                aria-label="Toggle navigation">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div class="min-w-0">
            <h2 class="font-headline-lg text-headline-lg text-on-surface truncate">
                @yield('page-title', 'Welcome back')
            </h2>
            <p class="font-body-md text-body-md text-on-surface-variant truncate">
                @yield('page-subtitle', "Here's what's happening with your discography today.")
            </p>
        </div>
    </div>

    <div class="flex items-center gap-sm shrink-0">
        @hasSection('actions')
            @yield('actions')
        @else
            <button class="hidden md:flex items-center gap-2 px-md py-3 rounded-xl border border-outline-variant text-on-surface font-label-lg text-label-lg hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Export Data
            </button>
            <a href="{{ route('artist-studio.releases.create') }}"
               class="flex items-center gap-2 px-md py-3 rounded-xl bg-primary-container text-on-primary-container font-bold text-label-lg hover:brightness-105 active:scale-95 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span class="hidden sm:inline">Create New Release</span>
                <span class="sm:hidden">New</span>
            </a>
        @endif
    </div>
</header>
