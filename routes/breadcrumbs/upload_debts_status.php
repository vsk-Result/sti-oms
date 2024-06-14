<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('upload_debts_status.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index upload-debts-status') ? route('upload_debts_status.index') : null;
    $trail->parent('home');
    $trail->push('Статус загруженных файлов по долгам объектов', $route);
});