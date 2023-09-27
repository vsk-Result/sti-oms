<?php

use App\Models\Writeoff;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('writeoffs.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index writeoffs') ? route('writeoffs.index') : null;
    $trail->parent('home');
    $trail->push('Списания', $route);
});

Breadcrumbs::for('writeoffs.create', function (BreadcrumbTrail $trail) {
    $trail->parent('writeoffs.index');
    $trail->push('Новое списание');
});

Breadcrumbs::for('writeoffs.show', function (BreadcrumbTrail $trail, Writeoff $writeoff) {
    $route = auth()->user()->can('show writeoffs') ? route('writeoffs.show', $writeoff) : null;
    $trail->parent('writeoffs.index');
    $trail->push('Списание за ' . $writeoff->getDateFormatted(), $route);
});

Breadcrumbs::for('writeoffs.edit', function (BreadcrumbTrail $trail, Writeoff $writeoff) {
    $trail->parent('writeoffs.show', $writeoff);
    $trail->push('Изменение списания');
});

Breadcrumbs::for('writeoffs.history.index', function (BreadcrumbTrail $trail) {
    $trail->parent('writeoffs.index');
    $trail->push('История списаний');
});
