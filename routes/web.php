<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SmtpSettingController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostInteractionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// "/" ekata awama splash page ekata yawanawa (splash eken passe posts.index ekata yanawa)
Route::get('/', function () {
    return redirect()->route('splash');
});

// Splash screen eka (resources/views/splash.blade.php)
Route::get('/splash', function () {
    return view('splash');
})->name('splash');

// Dashboard route eka wenas kale na (login weddi readers la redirect wena nisa)
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN AREA - "/admin" prefix eka
| "admin" middleware wenuwata "auth" + hama route ekakatama can:permission dala thiyenawa.
| Admin ta okkoma allow (Gate::before), reader ta Permission Manager eke
| tick karapu ewa witharai. Route names wenas wela na.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));

    // Mage posts / drafts
    Route::get('/posts/my', [PostController::class, 'myPosts'])
        ->middleware('can:posts.create')->name('posts.my');
    Route::get('/posts/drafts', [PostController::class, 'drafts'])
        ->middleware('can:posts.create')->name('posts.drafts');

    // Approval balagena inna posts (admin ta witharai, controller eke isAdmin check eka thiyenawa)
    Route::get('/posts/pending', [PostController::class, 'pending'])->name('posts.pending');
    Route::patch('/posts/{post}/approve', [PostController::class, 'approve'])->name('posts.approve');
    Route::patch('/posts/{post}/reject', [PostController::class, 'reject'])->name('posts.reject');

    // Trash ({id} wenuwata {slug} paawichchi karanawa)
    Route::get('/posts/trash', [PostController::class, 'trash'])
        ->middleware('can:posts.delete')->name('posts.trash');
    Route::patch('/posts/{slug}/restore', [PostController::class, 'restore'])
        ->middleware('can:posts.delete')->name('posts.restore');
    Route::delete('/posts/{slug}/force', [PostController::class, 'forceDelete'])
        ->middleware('can:posts.delete')->name('posts.force');

    // create, store, edit, update, destroy (resource eka wenuwata route ekakata wenama permission)
    Route::get('/posts/create', [PostController::class, 'create'])
        ->middleware('can:posts.create')->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])
        ->middleware('can:posts.create')->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])
        ->middleware('can:posts.edit')->name('posts.edit');
    Route::match(['put', 'patch'], '/posts/{post}', [PostController::class, 'update'])
        ->middleware('can:posts.edit')->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->middleware('can:posts.delete')->name('posts.destroy');

    // Categories
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('can:categories.manage');

    // Analytics
    Route::get('/analytics', AnalyticsController::class)
        ->middleware('can:analytics.view')->name('analytics');

    // SMTP settings
    Route::middleware('can:smtp.manage')->group(function () {
        Route::get('/smtp', [SmtpSettingController::class, 'edit'])->name('admin.smtp');
        Route::put('/smtp', [SmtpSettingController::class, 'update'])->name('admin.smtp.update');
        Route::post('/smtp/test', [SmtpSettingController::class, 'test'])->name('admin.smtp.test');
    });

    // Activity Log (admin ta witharai, controller eke role check eka thiyenawa)
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs');
    Route::delete('/activity-logs/clear', [ActivityLogController::class, 'clear'])->name('admin.activity-logs.clear');

    // Permission Manager (roles + users)
    Route::middleware('can:permissions.manage')
        ->prefix('permissions')
        ->name('permissions.')
        ->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('/', [PermissionController::class, 'store'])->name('store');                    // aluth permission ekak add karanawa
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');  // permission ekak delete karanawa
            Route::put('/roles/{role}', [PermissionController::class, 'updateRole'])->name('roles.update');
            Route::put('/users/{user}', [PermissionController::class, 'updateUserRole'])->name('users.update');
        });
});

/*
|--------------------------------------------------------------------------
| LOGIN weccha ayata (readers + admin): save, like, comment, follow, profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/posts/saved', [PostInteractionController::class, 'saved'])->name('posts.saved');

    // Like / Bookmark (can: permission check ekath ekka)
    Route::post('/posts/{post}/like', [PostInteractionController::class, 'like'])
        ->middleware('can:posts.like')->name('posts.like');
    Route::post('/posts/{post}/bookmark', [PostInteractionController::class, 'bookmark'])
        ->middleware('can:posts.bookmark')->name('posts.bookmark');

    // Comments (comment ekak danna can:comments.create ona)
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
        ->middleware('can:comments.create')->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Follow system eka
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');
    Route::get('/following', [FollowController::class, 'feed'])->name('following');

    // Notifications (All / Unread pennana page eka)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::get('/notifications/{id}', [NotificationController::class, 'read'])->name('notifications.read');

    // Breeze profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Avatar (profile pinthuraya)
    Route::post('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [AvatarController::class, 'destroy'])->name('profile.avatar.destroy');
});

/*
|--------------------------------------------------------------------------
| GOOGLE LOGIN (login wela inna ona na)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| PUBLIC (login wela inna ona na): home + post kiyawima
|--------------------------------------------------------------------------
*/
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
// {post} dan slug ekak (Post::getRouteKeyName), e nisa whereNumber('post') ain kala
Route::get('/posts/{post}', [PostController::class, 'show'])
    ->name('posts.show');

require __DIR__.'/auth.php';