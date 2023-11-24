<?php

use App\Http\Controllers\Helpdesk\TicketController;
use App\Http\Controllers\Helpdesk\TicketCloseController;
use App\Http\Controllers\Helpdesk\TicketOpenController;
use App\Http\Controllers\Helpdesk\AnswerController;

// Обращения

Route::get('helpdesk/tickets', [TicketController::class, 'index'])->name('helpdesk.tickets.index');
Route::get('helpdesk/tickets/create', [TicketController::class, 'create'])->name('helpdesk.tickets.create');
Route::post('helpdesk/tickets', [TicketController::class, 'store'])->name('helpdesk.tickets.store');
Route::get('helpdesk/tickets/{ticket}', [TicketController::class, 'show'])->name('helpdesk.tickets.show');
Route::get('helpdesk/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('helpdesk.tickets.edit');
Route::post('helpdesk/tickets/{ticket}', [TicketController::class, 'update'])->name('helpdesk.tickets.update');
Route::delete('helpdesk/tickets/{ticket}', [TicketController::class, 'destroy'])->name('helpdesk.tickets.destroy');

// Закрытие и открытие обращения

Route::post('helpdesk/tickets/{ticket}/close', [TicketCloseController::class, 'store'])->name('helpdesk.tickets.close');
Route::post('helpdesk/tickets/{ticket}/open', [TicketOpenController::class, 'store'])->name('helpdesk.tickets.open');

// Обращения - ответы

Route::post('helpdesk/tickets/{ticket}/answers', [AnswerController::class, 'store'])->name('helpdesk.tickets.answers.store');
