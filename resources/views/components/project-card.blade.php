{{-- Project Card Component --}}
{{-- Individual project card for masonry grid with status badge and tech stack --}}
@props(['project'])

<a href="{{ route('projects.show', $project->slug) }}" 
   class="block bg-rose-pine-surface border border-rose-pine-overlay rounded-lg overflow-hidden hover:ring-1 hover:ring-rose-pine-gold/50 transition break-inside-avoid">
    
    {{-- Thumbnail --}}
    @if($project->thumbnail)
        <div class="w-full">
            <img src="{{ $project->thumbnail }}" 
                 alt="{{ $project->title }}" 
                 class="w-full h-auto object-cover">
        </div>
    @endif
    
    {{-- Content --}}
    <div class="p-5">
        {{-- Title --}}
        <h3 class="text-lg font-semibold text-rose-pine-text mb-2">
            {{ $project->title }}
        </h3>
        
        {{-- Short Description --}}
        @if($project->short_description)
            <p class="text-sm text-rose-pine-subtle line-clamp-3 mb-4">
                {{ $project->short_description }}
            </p>
        @endif
        
        {{-- Status Badge --}}
        @php
            $statusColors = [
                'active' => 'bg-rose-pine-foam/20 text-rose-pine-foam',
                'completed' => 'bg-rose-pine-gold/20 text-rose-pine-gold',
                'in-progress' => 'bg-rose-pine-gold/20 text-rose-pine-gold',
                'archived' => 'bg-rose-pine-muted/20 text-rose-pine-muted',
            ];
            $statusColor = $statusColors[$project->status] ?? 'bg-rose-pine-muted/20 text-rose-pine-muted';
        @endphp
        
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }} capitalize mb-3">
            {{ str_replace('-', ' ', $project->status) }}
        </span>
        
        {{-- Tech Stack --}}
        @if(!empty($project->tech_stack))
            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach(array_slice($project->tech_stack, 0, 5) as $tech)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-rose-pine-overlay text-rose-pine-subtle">
                        {{ $tech }}
                    </span>
                @endforeach
                @if(count($project->tech_stack) > 5)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-rose-pine-overlay text-rose-pine-subtle">
                        +{{ count($project->tech_stack) - 5 }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</a>
