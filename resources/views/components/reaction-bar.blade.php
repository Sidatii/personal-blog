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
                        reactable_type: '{{ get_class($reactable) }}',
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
    class="border-t border-surface pt-4 mt-8"
>
    <div class="flex items-center gap-4 flex-wrap">
        <span class="text-sm text-subtle font-medium">React:</span>
        
        <div class="flex items-center gap-2">
            @foreach($types as $type => $emoji)
                <button
                    type="button"
                    @click="toggle('{{ $type }}')"
                    :class="{
                        'bg-love/20 ring-1 ring-love text-love': hasReacted('{{ $type }}'),
                        'bg-surface hover:bg-overlay text-text': !hasReacted('{{ $type }}'),
                        'opacity-50 cursor-not-allowed': isLoading
                    }"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-200 hover:scale-105 active:scale-95"
                    :disabled="isLoading"
                    title="{{ ucwords(str_replace('_', ' ', $type)) }}"
                >
                    <span class="text-lg">{{ $emoji }}</span>
                    <span 
                        x-text="getCount('{{ $type }}')"
                        x-show="getCount('{{ $type }}') > 0"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-0"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-0"
                        class="text-sm font-medium"
                    ></span>
                </button>
            @endforeach
        </div>
    </div>
</div>
@endif
