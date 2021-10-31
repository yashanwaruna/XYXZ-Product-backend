<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $posts = Post::With("user","comments");
        if(\Auth::user()->role_id != User::ROLE_ADMIN){
            $posts = $posts->where("status",Post::STATUS_APPROVED);
        }
        if($request->has('param') && $request->query('param') != ""){
            $query = $request->query('param');
            $posts = $posts->where('description', 'like', '%'.$query.'%')
                    ->orWhereHas('user', function($q)use($query){
                       $q->where('name', 'like', '%'.$query.'%');
                    });
        }
        
        return sendResponse(PostResource::collection($posts->get()), 'Ok');
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
            // 'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);
        
        try {
            $post    = Post::create([                
                'description' => $request->description,
                'user_id' => \Auth::user()->id,
                'product_id' => Product::first()->id,
                'status' => \Auth::user()->role_id == User::ROLE_ADMIN ? Post::STATUS_APPROVED : Post::STATUS_PENDING
            ]);
            $success = new PostResource($post);
            $message = 'Ok';
        } catch (Exception $e) {
            $success = [];
            $message = 'Oops! Unable to create a new post.';
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
        $post = Post::With("user","comments")->find($id);
        if (is_null($post)) return sendError('Post not found.');
        return sendResponse(new PostResource($post), 'Post retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post    $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {
            $post->status = $request->status;
            $post->save();

            $success = new PostResource($post);
            $message = 'Ok';
        } catch (Exception $e) {
            $success = [];
            $message = 'Oops, Failed to update the post.';
        }
        return sendResponse($success, $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        try {
            $post->delete();
            return sendResponse([], 'Ok');
        } catch (Exception $e) {
            return sendError('Oops! Unable to delete post.');
        }
    }
}
