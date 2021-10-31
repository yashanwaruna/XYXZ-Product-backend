<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $comments = Comment::all();

        return sendResponse(CommentResource::collection($comments), 'Ok');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|max:255',
            'post_id' => 'required|exists:posts,id'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);
        
        try {
            $comment    = Comment::create([                
                'description' => $request->description,
                'user_id' => \Auth::user()->id,
                'post_id' => $request->post_id
            ]);
            $success = new CommentResource($comment);
            $message = 'Ok';
        } catch (Exception $e) {
            $success = [];
            $message = 'Oops! Unable to create a new comment.';
        }

        return sendResponse($success, $message);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $comment = Comment::find($id);

        if (is_null($comment)) return sendError('Comment not found.');

        return sendResponse(new CommentResource($comment), 'Comment retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Comment    $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Comment $comment)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|max:255'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {
            $comment->description = $request->description;
            $comment->save();

            $success = new CommentResource($comment);
            $message = 'Ok';
        } catch (Exception $e) {
            $success = [];
            $message = 'Oops, Failed to update the comment.';
        }

        return sendResponse($success, $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        try {
            $comment->delete();
            return sendResponse([], 'Ok');
        } catch (Exception $e) {
            return sendError('Oops! Unable to delete comment.');
        }
    }
}
