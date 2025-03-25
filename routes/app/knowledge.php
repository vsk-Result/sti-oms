<?php

use App\Http\Controllers\Knowledge\KnowledgeController;

// База знаний

Route::get('knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
