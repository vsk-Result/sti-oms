<?php

use App\Http\Controllers\FavouriteLinkController;

// Избранные ссылки в быстром переходе

Route::get('favourite-links', [FavouriteLinkController::class, 'index'])->name('favourite_links.index');
Route::post('favourite-links', [FavouriteLinkController::class, 'store'])->name('favourite_links.store');
