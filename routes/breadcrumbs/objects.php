<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use App\Models\Object\BObject;

Breadcrumbs::for('objects.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index objects') ? route('objects.index') : null;
    $trail->parent('home');
    $trail->push('Объекты', $route);
});

Breadcrumbs::for('objects.create', function (BreadcrumbTrail $trail) {
    $trail->parent('objects.index');
    $trail->push('Новый объект');
});

Breadcrumbs::for('objects.show', function (BreadcrumbTrail $trail, BObject $object) {
    $route = auth()->user()->can('show objects') ? route('objects.show', $object) : null;
    $trail->parent('objects.index');
    $trail->push($object->getName(), $route);
});

Breadcrumbs::for('objects.edit', function (BreadcrumbTrail $trail, BObject $object) {
    $trail->parent('objects.show', $object);
    $trail->push('Изменение объекта');
});
