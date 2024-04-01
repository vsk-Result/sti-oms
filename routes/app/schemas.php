<?php

use App\Http\Controllers\Schemas\InteractionController;


// Схема взаимодействия

Route::get('schemas/interactions', [InteractionController::class, 'index'])->name('schemas.interactions.index');
