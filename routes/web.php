<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SongController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::get('/artist/{artist_id}', [ArtistController::class, 'show'])->name('artists.show');
Route::get('/artist/{artist_id}/description', [MemberController::class, 'index'])->name('artists.description');
Route::get('/artist/{artist_id}/{albumid}', [SongController::class, 'index'])->name('artists.songs');

require __DIR__.'/auth.php';
