@props([
    'reactable' => null,
    'types' => ['thumbs_up' => '👍', 'heart' => '❤️', 'celebrate' => '🎉', 'rocket' => '🚀']
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
        @foreach($types as $type => $emoji)
            <button
                type="button"
                @click="toggle('{{ $type }}')"
                :class="{
                    'bg-rose-pine-base border-rose-pine-love': hasReacted('{{ $type }}'),
                    'border-rose-pine-highlight hover:border-rose-pine-subtle': !hasReacted('{{ $type }}'),
                    'opacity-50 cursor-not-allowed': isLoading
                }"
                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border-2 transition-all duration-200 hover:scale-105 active:scale-95"
                :disabled="isLoading"
                title="{{ ucwords(str_replace('_', ' ', $type)) }}"
            >
                <span class="text-base">{{ $emoji }}</span>
                <span
                    x-text="getCount('{{ $type }}')"
                    x-show="getCount('{{ $type }}') > 0"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-0"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-0"
                    class="text-xs font-medium text-rose-pine-text"
                ></span>
            </button>
        @endforeach
    </div>
</div>
@endif
