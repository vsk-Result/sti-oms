<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use App\Models\Contract\Contract;

Breadcrumbs::for('contracts.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index contracts') ? route('contracts.index') : null;
    $trail->parent('home');
    $trail->push('Договора', $route);
});

Breadcrumbs::for('contracts.create', function (BreadcrumbTrail $trail) {
    $trail->parent('contracts.index');
    $trail->push('Новый договор');
});

Breadcrumbs::for('contracts.edit', function (BreadcrumbTrail $trail, Contract $contract) {
    $trail->parent('contracts.index');
    $trail->push('Изменение договора ' . $contract->getName());
});
