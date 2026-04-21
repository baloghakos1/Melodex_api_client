<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AlbumCrudController;
use App\Http\Controllers\SongCrudController;
use App\Http\Controllers\ArtistCrudController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/artists.index', [ArtistController::class, 'index'])->middleware(['auth'])->name('dashboard');

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
Route::get('/artist/{artist_id}/description', [ArtistController::class, 'description'])->name('artists.description');
Route::get('/artist/{artist_id}/{album_id}', [SongController::class, 'index'])->name('artists.songs');

Route::get('/crud.albums', [AlbumCrudController::class, 'index'])->name('crud.albums');
Route::get('/crud.songs', [SongCrudController::class, 'index'])->name('crud.songs');
Route::get('/crud.artists', [ArtistCrudController::class, 'index'])->name('crud.artists');

Route::resource('albumcrud',AlbumCrudController::class);
Route::resource('songcrud',SongCrudController::class);
Route::resource('artistcrud',ArtistCrudController::class);

/*
Route::get('export/artists/csv', [ArtistCrudController::class, 'exportCsv'])->name('export.artists.csv');
Route::get('export/albums/csv', [AlbumCrudController::class, 'exportCsv'])->name('export.albums.csv');
Route::get('export/songs/csv', [SongCrudController::class, 'exportCsv'])->name('export.songs.csv');

Route::get('export/artists/pdf', [ArtistCrudController::class, 'exportPdf'])->name('export.artists.pdf');
Route::get('export/albums/pdf', [AlbumCrudController::class, 'exportPdf'])->name('export.albums.pdf');
Route::get('export/songs/pdf', [SongCrudController::class, 'exportPdf'])->name('export.songs.pdf');
*/

Route::resource('playlists',PlaylistController::class);
Route::get('/playlists/{id}/songs', [PlaylistController::class, 'songs'])->name('playlists.songs');
Route::delete('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'removeSong'])->name('playlist.removeSong');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/preview', [SearchController::class, 'preview'])->name('search.preview');
Route::post('/songs/{song}/sync-playlists', [PlaylistController::class, 'syncSongPlaylists'])->name('playlist.syncSongPlaylists');

require __DIR__.'/auth.php';
