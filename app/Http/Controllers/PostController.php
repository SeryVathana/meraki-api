<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Support\Facades\Gate;
use Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $post = Post::where("status", "public")->get();
        $data = [
            'status' => 200,
            'post' => $post
        ];

        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {

        $user = Auth::user();
        $userId = $user->id;
        $userName = $user->username;

        $validator = Validator::make($request->all(), [
            'group_id' => 'nullable',
            'title' => 'required',
            'description' => 'nullable|max:255',
            'img_url' => 'required',
            'status' => 'required',
        ]);



        if ($validator->fails()) {

            $data = [
                "status" => 400,
                "message" => $validator->messages()
            ];

            return response()->json($data, 400);

        } else {
            $post = new Post;

            $post->user_id = $userId;
            $post->group_id = $request->group_id;
            $post->title = $request->title;
            $post->description = $request->description;
            $post->likes = '[]';
            $post->img_url = $request->img_url;
            $post->status = $request->status;

            $post->save();

            $data = [
                "status" => 200,
                "message" => "Post created successfully"
            ];

            return response()->json($data, 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            $data = [
                "status" => 404,
                "message" => "Post with id: $id is not found",
            ];

            return response()->json($data, 404);
        } else {
            $data = [
                "status" => 200,
                "post" => $post,
            ];

            return response()->json($data, 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post, )
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Post $post, Request $request)
    {

        if (!Gate::allows('update', $post)) {
            $data = [
                "status" => 403,
                "message" => "Unauthorized"
            ];

            return response()->json($data, 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'nullable|max:255',
            'img_url' => 'required',
            'status' => 'required',
        ]);



        if ($validator->fails()) {

            $data = [
                "status" => 400,
                "message" => $validator->messages()
            ];

            return response()->json($data, 400);

        } else {
            // $post = Post::find($id);

            // if(!$post) {
            //     $data = [
            //         "status"=>404,
            //         "message"=>"Post with id: $id is not found",
            //     ];

            //     return response()->json($data, 404);
            // }

            $post->title = $request->get('title');
            $post->description = $request->get('description');
            $post->img_url = $request->get('img_url');
            $post->status = $request->get('status');

            $post->save();

            $data = [
                "status" => 200,
                "message" => "Post updated successfully"
            ];

            return response()->json($data, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            $data = [
                "status" => 404,
                "message" => "Post with id: $id is not found",
            ];

            return response()->json($data, 404);
        } else {
            $post->delete();

            $data = [
                "status" => 200,
                "message" => "Post deleted successfully",
            ];

            return response()->json($data, 200);
        }
    }
}
