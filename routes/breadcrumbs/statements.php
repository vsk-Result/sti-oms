<?php

use App\Models\Statement;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('statements.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index statements') ? route('statements.index') : null;
    $trail->parent('home');
    $trail->push('Выписки', $route);
});

Breadcrumbs::for('statements.create', function (BreadcrumbTrail $trail) {
    $trail->parent('statements.index');
    $trail->push('Загрузить выписку');
});

Breadcrumbs::for('statements.show', function (BreadcrumbTrail $trail, Statement $statement) {
    $route = auth()->user()->can('show statements') ? route('statements.show', $statement) : null;
    $trail->parent('statements.index');
    $trail->push('Выписка за ' . $statement->getDateFormatted(), $route);
});

Breadcrumbs::for('statements.edit', function (BreadcrumbTrail $trail, Statement $statement) {
    $trail->parent('statements.show', $statement);
    $trail->push('Изменение выписки');
});
