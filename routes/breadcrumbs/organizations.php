<?php

use App\Models\Organization;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('organizations.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index organizations') ? route('organizations.index') : null;
    $trail->parent('home');
    $trail->push('Оргазинации', $route);
});

Breadcrumbs::for('organizations.create', function (BreadcrumbTrail $trail) {
    $trail->parent('organizations.index');
    $trail->push('Новая организация');
});

Breadcrumbs::for('organizations.show', function (BreadcrumbTrail $trail, Organization $organization) {
    $route = auth()->user()->can('show organizations') ? route('organizations.show', $organization) : null;
    $trail->parent('organizations.index');
    $trail->push($organization->name, $route);
});

Breadcrumbs::for('organizations.edit', function (BreadcrumbTrail $trail, Organization $organization) {
    $trail->parent('organizations.show', $organization);
    $trail->push('Изменение организации');
});
