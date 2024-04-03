<?php

use App\Http\Controllers\Schema\InteractionController;


// Схема взаимодействия

Route::get('schemas/interactions', [InteractionController::class, 'index'])->name('schemas.interactions.index');
Route::post('schemas/interactions', [InteractionController::class, 'update'])->name('schemas.interactions.update');
Route::get('schemas/interactions/edit', [InteractionController::class, 'edit'])->name('schemas.interactions.edit');
