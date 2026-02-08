@props([
    'reactable' => null,
    'types' => [
        'thumbs_up' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-.971a6.043 6.043 0 00-1.844-.288H6.633c-1.035 0-1.883-.848-1.883-1.884V12.383c0-1.036.848-1.883 1.883-1.883z" />',
        'heart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />',
        'celebrate' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />',
        'rocket' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />'
    ]
])

@if($reactable)
<div 
    x-data="{
        reactions: {{ json_encode($reactable->reaction_counts) }},
        userReactions: {{ json_encode($reactable->getUserReactionsAttribute()) }},
        isLoading: false,
        
        async toggle(type) {
            if (this.isLoading) return;
            
            this.isLoading = true;
            
            try {
                const response = await fetch('{{ route('reactions.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        reactable_type: '{{ class_basename($reactable) }}',
                        reactable_id: {{ $reactable->id }},
                        reaction_type: type
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Reaction request failed');
                }
                
                const data = await response.json();
                this.reactions = data.reactions;
                this.userReactions = data.user_reactions;
            } catch (error) {
                console.error('Failed to toggle reaction:', error);
            } finally {
                this.isLoading = false;
            }
        },
        
        hasReacted(type) {
            return this.userReactions.includes(type);
        },
        
        getCount(type) {
            return this.reactions[type] || 0;
        }
    }"
    class="border-t border-rose-pine-highlight pt-4 mt-6"
>
    <div class="flex items-center gap-2 flex-wrap">
        @foreach($types as $type => $svgPath)
            <button
                type="button"
                @click="toggle('{{ $type }}')"
                :class="{
                    'border-rose-pine-gold': hasReacted('{{ $type }}'),
                    'border-rose-pine-subtle hover:border-rose-pine-muted': !hasReacted('{{ $type }}'),
                    'opacity-50 cursor-not-allowed': isLoading
                }"
                class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg border-2 transition-all duration-200 hover:scale-105 active:scale-95 group"
                :disabled="isLoading"
                title="{{ ucwords(str_replace('_', ' ', $type)) }}"
            >
                <svg
                    class="w-4 h-4 transition-colors duration-200"
                    :class="{
                        'stroke-rose-pine-gold': hasReacted('{{ $type }}'),
                        'stroke-rose-pine-subtle group-hover:stroke-rose-pine-text': !hasReacted('{{ $type }}')
                    }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                >
                    {!! $svgPath !!}
                </svg>
                <span
                    x-text="getCount('{{ $type }}')"
                    x-show="getCount('{{ $type }}') > 0"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-0"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-0"
                    :class="{
                        'text-rose-pine-gold': hasReacted('{{ $type }}'),
                        'text-rose-pine-text': !hasReacted('{{ $type }}')
                    }"
                    class="text-xs font-semibold"
                ></span>
            </button>
        @endforeach
    </div>
</div>
@endif
