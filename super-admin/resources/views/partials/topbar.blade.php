<header class="sticky top-0 z-30 glass-header px-container-margin h-16 flex items-center justify-between border-b border-outline-variant/15">
    <div class="flex items-center gap-sm min-w-0">
        <button id="sidebar-toggle"
                class="lg:hidden w-10 h-10 rounded-lg bg-surface-container hover:bg-surface-container-high flex items-center justify-center"
                aria-label="Toggle navigation">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div class="hidden md:flex items-center gap-xs text-secondary text-label-md">
            <span class="material-symbols-outlined text-[18px]">terminal</span>
            <span class="uppercase tracking-widest">@yield('breadcrumb', 'Super Admin')</span>
        </div>
    </div>

    <div class="flex items-center gap-sm">
        <div class="hidden md:flex items-center gap-xs bg-surface-container rounded-full px-md h-10 w-72 max-w-[40vw]">
            <span class="material-symbols-outlined text-secondary text-[20px]">search</span>
            <input type="search"
                   placeholder="Search tokens, users, assets…"
                   class="bg-transparent outline-none w-full text-body-md text-on-surface placeholder:text-outline" />
            <span class="text-label-sm text-secondary border border-outline-variant rounded px-1">⌘K</span>
        </div>

        <button class="relative w-10 h-10 rounded-full bg-surface-container hover:bg-surface-container-high flex items-center justify-center" aria-label="Notifications">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-primary"></span>
        </button>

        <div class="flex items-center gap-sm pl-sm border-l border-outline-variant/30">
            <div class="hidden md:flex flex-col items-end leading-tight">
                <span class="text-[13px] font-bold text-on-surface">Ken Reyes</span>
                <span class="text-[11px] text-primary uppercase tracking-widest">root</span>
            </div>
            <div class="w-10 h-10 rounded-full overflow-hidden border border-outline-variant">
                <img alt="Admin"
                     class="w-full h-full object-cover"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuB0W58-ygqCd1qAadO-muDbOU03zpweHwu13YKaome89FNehyOt0p-cmyDaFvbXGQ24JijzLLZyyZVha7fqhsqMImRrJt4NGmdK0r7cGlK3IBc7O1d-d7V1mGOdLwX51Og_gAaGdXyZ9-_X6xt05gX5zGKsSYdwmtkJ4kAzWKZW7ipDeUEntHXcXguOmosr2nowXW2k-ukGF3ojukpeFLnJfwcAJa-kdRrbEs8bHC97Ee9-Alol2tED9JPBU4eBv7PuzW-YoKZ1Guo">
            </div>
        </div>
    </div>
</header>
