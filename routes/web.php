<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AlbumCrudController;
use App\Http\Controllers\SongCrudController;
use App\Http\Controllers\ArtistCrudController;
use App\Http\Controllers\MemberCrudController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/crud.index', function () {
    return view('crud.index');
})->name('crud.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::get('/artist/{artist_id}', [ArtistController::class, 'show'])->name('artists.show');
Route::get('/artist/{artist_id}/description', [MemberController::class, 'index'])->name('artists.description');
Route::get('/artist/{artist_id}/{albumid}', [SongController::class, 'index'])->name('artists.songs');

Route::get('/crud.albums', [AlbumCrudController::class, 'index'])->name('crud.albums');
Route::get('/crud.songs', [SongCrudController::class, 'index'])->name('crud.songs');
Route::get('/crud.artists', [ArtistCrudController::class, 'index'])->name('crud.artists');
Route::get('/crud.members', [MemberCrudController::class, 'index'])->name('crud.members');

Route::resource('albumcrud',AlbumCrudController::class);
Route::resource('songcrud',SongCrudController::class);
Route::resource('artistcrud',ArtistCrudController::class);
Route::resource('membercrud',MemberCrudController::class);

Route::get('export/artists/csv', [ArtistCrudController::class, 'exportCsv'])->name('export.artists.csv');
Route::get('export/artists/pdf', [ArtistCrudController::class, 'exportPdf'])->name('export.artists.pdf');



require __DIR__.'/auth.php';
