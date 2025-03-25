<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('knowledge.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('База знаний', route('knowledge.index'));
});
