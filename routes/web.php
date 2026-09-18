<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Public Guest-Accessible Routes
Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
Route::get('/groups', [\App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/toggle-premium', function(\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $user->is_premium = !$user->is_premium;
        $user->save();

        \App\Models\UserActivity::log(
            $user,
            'premium_toggled',
            $user->is_premium ? 'Upgraded to PRO membership' : 'Downgraded to FREE account',
            ['is_premium' => $user->is_premium],
            $request
        );

        return back();
    })->name('profile.toggle-premium');

    // Groups Actions
    Route::get('/groups/create', [\App\Http\Controllers\GroupController::class, 'create'])->name('groups.create')->middleware(\App\Http\Middleware\EnsurePremiumUser::class);
    Route::post('/groups', [\App\Http\Controllers\GroupController::class, 'store'])->name('groups.store')->middleware(\App\Http\Middleware\EnsurePremiumUser::class);
    Route::post('/groups/{group}/join', [\App\Http\Controllers\GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [\App\Http\Controllers\GroupController::class, 'leave'])->name('groups.leave');
    Route::post('/groups/{group}/posts', [\App\Http\Controllers\GroupController::class, 'storePost'])->name('groups.posts.store');

    // News Comments Actions
    Route::post('/news/{news}/comments', [\App\Http\Controllers\NewsController::class, 'storeComment'])->name('news.comments.store');
});

// Register parameterized show route last to avoid matching /groups/create
Route::get('/groups/{group}', [\App\Http\Controllers\GroupController::class, 'show'])->name('groups.show');

require __DIR__.'/auth.php';
