<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('schemas.interactions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Схема взаимодействия', route('schemas.interactions.index'));
});
