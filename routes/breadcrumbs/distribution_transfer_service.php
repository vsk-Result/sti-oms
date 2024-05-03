<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('distribution_transfer_service.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index distribution-transfer-service') ? route('distribution_transfer_service.index') : null;
    $trail->parent('home');
    $trail->push('Распределение услуг по трансферу', $route);
});
