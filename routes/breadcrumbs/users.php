<?php

use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('users.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index admin-users') ? route('users.index') : null;
    $trail->parent('home');
    $trail->push('Пользователи', $route);
});

Breadcrumbs::for('users.show', function (BreadcrumbTrail $trail, User $user) {
    $route = auth()->user()->can('show admin-users') ? route('users.show', $user) : null;
    $trail->parent('users.index');
    $trail->push($user->name, $route);
});

Breadcrumbs::for('users.edit', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('users.show', $user);
    $trail->push('Настройки аккаунта', route('users.edit', $user));
});

Breadcrumbs::for('users.passwords.reset', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('users.edit', $user);
    $trail->push('Изменение пароля');
});
