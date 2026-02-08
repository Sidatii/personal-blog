<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    /**
     * The valid reaction types and their emojis.
     */
    protected array $validTypes = [
        'thumbs_up',
        'heart',
        'celebrate',
        'rocket',
    ];

    /**
     * Store or toggle a reaction.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reactable_type' => ['required', 'string'],
            'reactable_id' => ['required', 'integer'],
            'reaction_type' => ['required', 'string', 'in:'.implode(',', $this->validTypes)],
        ]);

        // Resolve the model class
        $modelClass = $this->resolveModelClass($validated['reactable_type']);

        if (! $modelClass) {
            return response()->json([
                'error' => 'Invalid reactable type',
            ], 422);
        }

        // Check if the model uses the Reactable trait
        if (! in_array('App\Models\Traits\Reactable', class_uses($modelClass))) {
            return response()->json([
                'error' => 'Model does not support reactions',
            ], 422);
        }

        // Find the reactable model
        $reactable = $modelClass::find($validated['reactable_id']);

        if (! $reactable) {
            return response()->json([
                'error' => 'Reactable not found',
            ], 404);
        }

        // Toggle the reaction
        $result = $reactable->toggleReaction(
            $validated['reaction_type'],
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'reactions' => $result['reaction_counts'],
            'user_reactions' => $result['user_reactions'],
            'added' => $result['added'],
        ]);
    }

    /**
     * Resolve the model class from the type string.
     */
    protected function resolveModelClass(string $type): ?string
    {
        // Map of allowed reactable types to their full class names
        $modelMap = [
            'post' => 'App\Models\Post',
            'Post' => 'App\Models\Post',
            'App\Models\Post' => 'App\Models\Post',
            'comment' => 'App\Models\Comment',
            'Comment' => 'App\Models\Comment',
            'App\Models\Comment' => 'App\Models\Comment',
        ];

        return $modelMap[$type] ?? null;
    }
}
