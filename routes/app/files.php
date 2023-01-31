<?php

use App\Http\Controllers\File\DownloadController;

// Скачивание файлов

Route::get('files/download/{file}', [DownloadController::class, 'index'])->name('files.download');
