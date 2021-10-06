<?php

use App\Models\Company;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('companies.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index companies') ? route('companies.index') : null;
    $trail->parent('home');
    $trail->push('Компании', $route);
});

Breadcrumbs::for('companies.create', function (BreadcrumbTrail $trail) {
    $trail->parent('companies.index');
    $trail->push('Новая компания');
});

Breadcrumbs::for('companies.show', function (BreadcrumbTrail $trail, Company $company) {
    $route = auth()->user()->can('show companies') ? route('companies.show', $company) : null;
    $trail->parent('companies.index');
    $trail->push($company->name, $route);
});

Breadcrumbs::for('companies.edit', function (BreadcrumbTrail $trail, Company $company) {
    $trail->parent('companies.show', $company);
    $trail->push('Изменение компании');
});
