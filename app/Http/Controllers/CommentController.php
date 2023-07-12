<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'comment' => 'required'
        ]);

        // Create a new comment instance
        $comment = new Comment();
        $comment->match_id = $validatedData['match_id'];
        $comment->user_id = auth()->user()->id;
        $comment->comment = $validatedData['comment'];
        $comment->save();

        return back()->with('success', 'Comment added successfully!');
    }

    /**
     * Update a comment.
     *
     * @param Request $request
     * @param  \App\Models\Comment  $comment
     * @return JsonResponse
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        if ($comment->user_id !== auth()->user()->id) {
            abort(403);
        }
        $comment->comment = $request->input('comment');
        $comment->save();
        return response()->json(['message' => 'Comment updated.'], Response::HTTP_OK);
    }

    /**
     * Delete a comment.
     *
     * @param  \App\Models\Comment  $comment
     * @return RedirectResponse
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        if ($comment->user_id !== auth()->user()->id) {
            abort(403);
        }
        // Delete the comment
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully!');
    }

}
