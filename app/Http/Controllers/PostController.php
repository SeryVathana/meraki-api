<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
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
            'posts' => $post
        ];

        return response()->json($data, 200);
    }

    public function getMyPosts()
    {
        $user = Auth::user();
        $userId = $user->id;

        $post = Post::where("user_id", $userId)->get();
        $data = [
            'status' => 200,
            'posts' => $post
        ];

        return response()->json($data, 200);
    }
    public function getUserPosts($id)
    {
        $curUser = Auth::user();
        $curUserId = $curUser->id;

        $user = User::find($id);
        if (!$user) {
            $data = [
                "status" => 404,
                "message" => "User not found",
            ];

            return response()->json($data, 404);
        }
        $post = "";

        if ($curUser->role == "admin") {
            $post = Post::where("user_id", $id)->get();
        } else {
            $post = Post::where("user_id", $id)->where("status", "public")->get();
        }

        $data = [
            'status' => 200,
            'posts' => $post
        ];

        return response()->json($data, 200);
    }

    public function getGroupPosts($id)
    {
        $user = Auth::user();
        $userId = $user->id;

        $authorized = true;

        $group = Group::find($id);
        if (!$group) {

            $data = [
                "status" => 404,
                "message" => "Group not found"
            ];

            return response()->json($data, 404);
        }

        $isGroupMember = GroupMember::where("group_id", $id)->where("user_id", $userId)->first();

        if ($group->status == "private" && $user->role != 'admin' && !$isGroupMember) {
            $authorized = false;
        }

        $posts = [];
        if ($authorized) {
            $posts = Post::where("group_id", $id)->get();

            $data = [
                "status" => 200,
                "posts" => $posts,
            ];

            return response()->json($data, 200);
        } else {
            $data = [
                "status" => 403,
                "message" => "Unathorized",
            ];

            return response()->json($data, 403);
        }
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

        }

        if ($request->status != "public" && $request->status != "private") {
            $data = [
                "status" => 400,
                "message" => "Invalid input"
            ];

            return response()->json($data, 400);
        }

        $post = new Post;

        if ($request->group_id != null) {
            $group1 = Group::find($request->group_id);
            if (!$group1) {
                $data = [
                    "status" => 404,
                    "message" => "Group not found"
                ];
                return response()->json($data, 404);
            }

            $post->status = $group1->status;
        } else {
            $post->status = $request->status;
        }


        $post->group_id = $request->group_id;
        $post->user_id = $userId;
        $post->group_id = $request->group_id;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->likes = '[]';
        $post->img_url = $request->img_url;

        $post->save();

        $data = [
            "status" => 200,
            "message" => "Post created successfully"
        ];

        return response()->json($data, 200);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $userId = $user->id;

        $post = Post::find($id);

        if (!$post) {
            $data = [
                "status" => 404,
                "message" => "Post not found",
            ];

            return response()->json($data, 404);
        }

        if ($post->status == "private" && $post->user_id != $userId && $user->role != "admin") {
            $data = [
                "status" => 403,
                "message" => "Unauthorized",
            ];

            return response()->json($data, 403);
        }

        $data = [
            "status" => 200,
            "post" => $post,
        ];

        return response()->json($data, 200);

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
    public function update(Request $request, $id)
    {

        $post = Post::find($id);

        if (!$post) {
            $data = [
                "status" => 404,
                "message" => "Post not found",
            ];

            return response()->json($data, 404);
        }

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

        }

        if ($request->status != "public" && $request->status != "private") {
            $data = [
                "status" => 400,
                "message" => "Invalid input"
            ];

            return response()->json($data, 400);
        }


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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $userId = $user->id;

        $post = Post::find($id);

        if (!$post) {
            $data = [
                "status" => 404,
                "message" => "Post not found",
            ];

            return response()->json($data, 404);
        }

        $authorized = false;

        if ($user->role != "admin") {
            if ($post->group_id != null) {
                $group = Group::find($post->group_id);
                if (!$group && $post->user_id != $userId) {
                    $authorized = false;
                }

                $member = GroupMember::where("group_id", $group->id)->where("user_id", $userId)->where("role", "admin")->first();
                if ($member) {
                    $authorized = true;
                }
            }
        }

        if ($post->status != "private" && $post->user_id == $userId && $user->role == "admin") {
            $authorized = true;
        }

        if (!$authorized) {
            $data = [
                "status" => 403,
                "message" => "Unauthorized",
            ];

            return response()->json($data, 403);
        }


        $post->delete();

        $data = [
            "status" => 200,
            "message" => "Post deleted successfully",
        ];

        return response()->json($data, 200);

    }
}
