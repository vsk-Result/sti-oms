<?php

use App\Models\Helpdesk\Ticket;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('helpdesk.tickets.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Обращения в поддержку', route('helpdesk.tickets.index'));
});

Breadcrumbs::for('helpdesk.tickets.create', function (BreadcrumbTrail $trail) {
    $trail->parent('helpdesk.tickets.index');
    $trail->push('Новое обращение');
});

Breadcrumbs::for('helpdesk.tickets.show', function (BreadcrumbTrail $trail, Ticket $ticket) {
    $trail->parent('helpdesk.tickets.index');
    $trail->push('Обращение #' . $ticket->id, route('helpdesk.tickets.show', $ticket));
});

Breadcrumbs::for('helpdesk.tickets.edit', function (BreadcrumbTrail $trail, Ticket $ticket) {
    $trail->parent('helpdesk.tickets.show', $ticket);
    $trail->push('Изменение обращения');
});
