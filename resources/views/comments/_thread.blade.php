@props(['comments' => [], 'depth' => 0])

@foreach($comments as $comment)
    @include('comments._comment', compact('comment', 'depth'))

    @if($comment->replies && $comment->replies->count() > 0 && $depth < (config('comments.max_depth', 5) - 1))
        <div class="thread-children ml-4">
            @include('comments._thread', [
                'comments' => $comment->replies,
                'depth' => $depth + 1
            ])
        </div>
    @endif
@endforeach