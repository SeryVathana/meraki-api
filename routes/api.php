<?php

use App\Http\Controllers\FolderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupInviteController;
use App\Http\Controllers\GroupMemberController;
use App\Http\Controllers\GroupRequestController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('auth/register', [UserController::class, 'createUser']);
Route::post('auth/login', [UserController::class, 'loginUser']);

Route::group([
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('post', [PostController::class, "index"]);
    Route::get('post/mypost', [PostController::class, "getMyPosts"]);
    Route::get('post/user/{id}', [PostController::class, "getUserPosts"]); // $id = user id
    Route::get('post/group/{id}', [PostController::class, "getGroupPosts"]); // $id = group id
    Route::get('post/{id}', [PostController::class, "show"]);
    Route::post('post', [PostController::class, "store"]);
    Route::put('post/{id}', [PostController::class, "update"]);
    Route::delete('post/{id}', [PostController::class, "destroy"]);

    Route::get('group', [GroupController::class, "index"]);
    Route::get('group/{id}', [GroupController::class, "show"]);
    Route::post('group', [GroupController::class, "store"]);
    Route::put('group/{id}', [GroupController::class, "update"]);
    Route::delete('group/{id}', [GroupController::class, "destroy"]);

    // Group member
    Route::get('group/member/{id}', [GroupMemberController::class, "show"]); // $id = group id
    Route::put('group/member/{id}', [GroupMemberController::class, "update"]); // $id = group id
    Route::delete('group/member/{id}', [GroupMemberController::class, "destroy"]); // $id = group id

    // Group invite
    Route::get('group/invite/{id}', [GroupInviteController::class, "index"]); //$id = group id
    Route::post('group/invite/{id}', [GroupInviteController::class, "store"]); //$id = group id
    Route::delete('group/invite/{id}', [GroupInviteController::class, "destroy"]); //$id = invite id
    Route::put('group/invite/accept/{id}', [GroupInviteController::class, "update"]); //$id = invite id

    //Group request
    Route::post('group/request/{id}', [GroupRequestController::class, "store"]); //$id = group id
    Route::put('group/request/accept/{id}', [GroupRequestController::class, "update"]); //$id = group id
    Route::delete('group/request/{id}', [GroupRequestController::class, "destroy"]); //$id = group id

    //Folder
    Route::get('folder', [FolderController::class, "index"]);
    Route::post('folder', [FolderController::class, "store"]);
    Route::put('folder/{id}', [FolderController::class, "update"]);
    Route::delete('folder/{id}', [FolderController::class, "destroy"]);
});
