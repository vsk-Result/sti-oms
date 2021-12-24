<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use App\Models\Contract\Act;

Breadcrumbs::for('acts.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index acts') ? route('acts.index') : null;
    $trail->parent('home');
    $trail->push('Акты', $route);
});

Breadcrumbs::for('acts.show', function (BreadcrumbTrail $trail, Act $act) {
    $route = auth()->user()->can('show acts') ? route('acts.show', $act) : null;
    $trail->parent('acts.index');
    $trail->push('Акт за ' . $act->getDateFormatted(), $route);
});

Breadcrumbs::for('acts.create', function (BreadcrumbTrail $trail) {
    $trail->parent('acts.index');
    $trail->push('Новый акт');
});

Breadcrumbs::for('acts.edit', function (BreadcrumbTrail $trail, Act $act) {
    $trail->parent('acts.show', $act);
    $trail->push('Изменение акта за ' . $act->getDateFormatted());
});
