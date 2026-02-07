{{-- Tech Stack Badges Component --}}
{{-- Categorized skill badges with Rose Pine colors and hover tooltips --}}
<div class="space-y-6">
    @foreach($categories as $categoryName => $skills)
        @if(count($skills) > 0)
            <div>
                <h3 class="text-lg font-semibold text-rose-pine-text mb-3">{{ $categoryName }}</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($skills as $skill)
                        @php
                            // Determine badge colors based on category
                            $colorClass = match($categoryName) {
                                'Languages' => 'bg-rose-pine-foam/15 text-rose-pine-foam border-rose-pine-foam/30',
                                'Frameworks & Libraries' => 'bg-rose-pine-iris/15 text-rose-pine-iris border-rose-pine-iris/30',
                                'Tools & Platforms' => 'bg-rose-pine-gold/15 text-rose-pine-gold border-rose-pine-gold/30',
                                'Specializations' => 'bg-rose-pine-love/15 text-rose-pine-love border-rose-pine-love/30',
                                default => 'bg-rose-pine-pine/15 text-rose-pine-pine border-rose-pine-pine/30',
                            };
                        @endphp

                        <div
                            x-data="{ showTooltip: false }"
                            @mouseenter="showTooltip = true"
                            @mouseleave="showTooltip = false"
                            class="relative inline-flex"
                        >
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border {{ $colorClass }} cursor-default transition-all hover:opacity-80">
                                {{ $skill['name'] }}
                            </span>

                            {{-- Tooltip --}}
                            <div
                                x-show="showTooltip"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 z-10 pointer-events-none"
                            >
                                <div class="bg-rose-pine-overlay text-rose-pine-text text-xs px-2 py-1 rounded shadow-lg whitespace-nowrap">
                                    {{ $skill['note'] }}
                                </div>
                                {{-- Tooltip arrow --}}
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                    <div class="w-2 h-2 bg-rose-pine-overlay rotate-45"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>
