<?php

use App\Events\testing;
use App\Http\Controllers\Auth\authenticate;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserManagement;
use App\TestMethod\SwitchMe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostViewController;
use App\Http\Controllers\CategoryController;

Route::post('/register',[authenticate::class , 'register']);
Route::post('/login',[authenticate::class , 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [UserProfileController::class, 'show']);
    Route::put('/profile', [UserProfileController::class, 'update']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::put('/update_user',[UserManagement::class , 'UpdateUserInfo']);
    Route::post('/logout' , [UserManagement::class , 'logout']);

    Route::get('/testView',function(){
        $test = new SwitchMe();
        $result = $test->useSwitch('testMethod');
        return response()->json([
            'msg'=>$result
        ]);
    });

    Route::prefix('users')->group(function(){
        Route::get('/', [UserManagement::class, 'ShowAll'])->middleware(['role:super_admin' , 'permission:view_roles']);
        Route::get('/trashed', [UserManagement::class, 'ShowTrashUsers'])->middleware(['role:super_admin' , 'permission:view_roles']);
        Route::get('/{user}', [UserManagement::class, 'GetUserDetails'])->middleware(['role:super_admin' , 'permission:view_roles']);
        Route::put('/roles/{role}', [UserManagement::class, 'UpdateRolePermissions'])->middleware(['role:super_admin' , 'permission:edit_roles']);
        Route::put('/{user}/role', [UserManagement::class, 'UpdateUserRole'])->middleware(['role:super_admin' , 'permission:edit_roles']);
        Route::delete('/{userId}/soft-delete', [UserManagement::class, 'SoftDeleteUser'])->middleware(['role:super_admin' , 'permission:delete_roles']);
        Route::post('/{userId}/restore', [UserManagement::class, 'RestoreUser'])->middleware(['role:super_admin' , 'permission:create_roles']);
        Route::delete('/{userId}/force-delete', [UserManagement::class, 'ForceDeleteUser'])->middleware(['role:super_admin' , 'permission:delete_roles']);
        Route::get('/auditlog/{userId}' , [UserManagement::class , 'getAuditLogs'])->middleware(['role:super_admin' , 'permission:delete_roles']);
    });

    Route::prefix('category')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

    Route::prefix('post')->group(function(){
        Route::get('user/posts', [UserPostController::class, 'index']);
    });

    Route::prefix('post_views')->group(function(){
        Route::post('posts/{postId}/view', [PostViewController::class, 'recordView']);
        Route::get('posts/{postId}/views', [PostViewController::class, 'getViewsByPost']);
        Route::get('users/{userId}/views', [PostViewController::class, 'getViewsByUser']);
        Route::get('posts/{postId}/check-view', [PostViewController::class, 'checkUserViewedPost']);
    });

    Route::prefix('topics')->group(function(){
        Route::get('/', [TopicController::class, 'index'])->name('topics.index'); 
        Route::post('/', [TopicController::class, 'store'])->name('topics.store'); 
        Route::get('/{id}', [TopicController::class, 'show'])->name('topics.show'); 
        Route::put('/{id}', [TopicController::class, 'update'])->name('topics.update'); 
        Route::delete('/{id}', [TopicController::class, 'destroy'])->name('topics.destroy');
        Route::get('/category/{categoryId}', [TopicController::class, 'getByCategory'])->name('topics.byCategory');

    });

    Route::prefix('upload_media')->group(function(){

    });

    Route::prefix('site_settings')->group(function(){

    });

    Route::prefix('post')->group(function () {
        Route::get('admin/posts', [PostController::class, 'index']); // Admin-only route
        
        // CRUD operations for posts
        Route::post('posts', [PostController::class, 'store']);
        Route::put('posts/{id}', [PostController::class, 'update']);
        Route::delete('posts/{id}', [PostController::class, 'softDelete']);
        Route::post('posts/{id}/restore', [PostController::class, 'restore']);
        Route::delete('posts/{id}/force', [PostController::class, 'forceDelete']);
        Route::get('trashed', [PostController::class, 'trashed']);
        
        // Publishing and interaction routes
        Route::post('posts/{id}/publish', [PostController::class, 'publish']);
        Route::post('posts/{id}/unpublish', [PostController::class, 'unpublish']);
        Route::post('posts/{id}/like', [PostController::class, 'like']);
        Route::delete('posts/{id}/like', [PostController::class, 'unlike']);
    });

});

//reverb
Route::post('/msg',function(Request $req){
    $bruh = $req->message;
    event(new testing($bruh));
    return response()->json([
        'msg'=>$bruh
    ]);
});

//Route::get('/read_image',function(){

    //$url = Storage::disk('s3')->temporaryUrl('images/qDzvxaOoXGMQCcxZ1WEXOC4dDDvPO1MQtMc0gYWK.jpg',now()->addHours(5));

    //return response()->json(['url' => $url]);
//});