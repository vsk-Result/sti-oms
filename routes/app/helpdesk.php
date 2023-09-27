<?php

use App\Http\Controllers\Helpdesk\TicketController;
use App\Http\Controllers\Helpdesk\AnswerController;

// Обращения

Route::get('helpdesk/tickets', [TicketController::class, 'index'])->name('helpdesk.tickets.index');
Route::get('helpdesk/tickets/create', [TicketController::class, 'create'])->name('helpdesk.tickets.create');
Route::post('helpdesk/tickets', [TicketController::class, 'store'])->name('helpdesk.tickets.store');
Route::get('helpdesk/tickets/{ticket}', [TicketController::class, 'show'])->name('helpdesk.tickets.show');
Route::get('helpdesk/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('helpdesk.tickets.edit');
Route::post('helpdesk/tickets/{ticket}', [TicketController::class, 'update'])->name('helpdesk.tickets.update');
Route::delete('helpdesk/tickets/{ticket}', [TicketController::class, 'destroy'])->name('helpdesk.tickets.destroy');

// Обращения - ответы

Route::post('helpdesk/tickets/{ticket}/answers', [AnswerController::class, 'store'])->name('helpdesk.tickets.answers.store');
