<?php

use App\Models\Guarantee;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('guarantees.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index guarantees') ? route('guarantees.index') : null;
    $trail->parent('home');
    $trail->push('Гарантийные удержания', $route);
});

Breadcrumbs::for('guarantees.create', function (BreadcrumbTrail $trail) {
    $trail->parent('guarantees.index');
    $trail->push('Новое гарантийное удержание');
});

Breadcrumbs::for('guarantees.show', function (BreadcrumbTrail $trail, Guarantee $guarantee) {
    $route = auth()->user()->can('show guarantees') ? route('guarantees.show', $guarantee) : null;
    $trail->parent('guarantees.index');
    $trail->push('Гарантийное удержание #' . $guarantee->id, $route);
});

Breadcrumbs::for('guarantees.edit', function (BreadcrumbTrail $trail, Guarantee $guarantee) {
    $trail->parent('guarantees.show', $guarantee);
    $trail->push('Изменение гарантийного удержания');
});
