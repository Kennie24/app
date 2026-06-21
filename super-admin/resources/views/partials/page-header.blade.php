{{-- $title, $subtitle, $eyebrow (optional), $actions (slot of buttons) --}}
<section class="reveal mb-lg flex flex-col md:flex-row md:items-end md:justify-between gap-md">
    <div class="min-w-0">
        @isset($eyebrow)
            <span class="text-label-md text-secondary uppercase tracking-widest">{{ $eyebrow }}</span>
        @endisset
        <h1 class="text-headline-lg font-bold mt-xs">{{ $title }}</h1>
        @isset($subtitle)
            <p class="text-body-md text-secondary mt-xs max-w-2xl">{{ $subtitle }}</p>
        @endisset
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-sm">{!! $actions !!}</div>
    @endisset
</section>
