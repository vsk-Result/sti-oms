<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function index(Request $request): View
    {
        $usersQuery = User::query();

        if ($request->has('status_id')) {
            $usersQuery->whereIn('status_id', $request->get('status_id'));
        } else {
            $usersQuery->where('status_id', Status::STATUS_ACTIVE);
        }

        $users = $usersQuery->withTrashed()->with('roles')->orderBy('name')->get();
        $now = Carbon::now();

        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();

        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $yearStart = $now->copy()->startOfYear();
        $yearEnd = $now->copy()->endOfYear();

        $usersToday = $users->filter(function ($user) use ($todayStart, $todayEnd) {
            $date = $user->lastSuccessfulLoginAt();

            if (! isset($date)) {
                return false;
            }

            return $date->between($todayStart, $todayEnd);
        });

        $usersWeek = $users->filter(function ($user) use ($weekStart, $weekEnd) {
            $date = $user->lastSuccessfulLoginAt();

            if (! isset($date)) {
                return false;
            }

            return $date->between($weekStart, $weekEnd);
        })->whereNotIn('id', $usersToday->pluck('id')->toArray());

        $usersMonth = $users->filter(function ($user) use ($monthStart, $monthEnd) {
            $date = $user->lastSuccessfulLoginAt();

            if (! isset($date)) {
                return false;
            }

            return $date->between($monthStart, $monthEnd);
        })->whereNotIn('id', array_merge($usersToday->pluck('id')->toArray(), $usersWeek->pluck('id')->toArray()));

        $usersYear = $users->filter(function ($user) use ($yearStart, $yearEnd) {
            $date = $user->lastSuccessfulLoginAt();

            if (! isset($date)) {
                return false;
            }

            return $date->between($yearStart, $yearEnd);
        })->whereNotIn('id', array_merge($usersToday->pluck('id')->toArray(), $usersWeek->pluck('id')->toArray(), $usersMonth->pluck('id')->toArray()));

        $usersOld = $users->filter(function ($user) use ($yearStart) {
            $date = $user->lastSuccessfulLoginAt();

            if (! isset($date)) {
                return false;
            }

            return $date->lt($yearStart);
        });

        $usersGroupedBySuccessLoginDate = [
            'Сегодня' => $usersToday,
            'На этой неделе' => $usersWeek,
            'В этом месяце' => $usersMonth,
            'В этом году' => $usersYear,
            'Давно' => $usersOld,
        ];
        $statuses = Status::getStatuses();

        return view('users.index', compact('usersGroupedBySuccessLoginDate', 'statuses'));
    }

    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    public function create(): View
    {
        if (! (auth()->user()->can('create admin-users'))) {
            abort(403);
        }

        return view('users.create');
    }

    public function store(CreateUserRequest $request): RedirectResponse
    {
        if (! (auth()->user()->can('create admin-users'))) {
            abort(403);
        }

        $this->userService->createUser($request->toArray());

        return redirect()->route('users.index');
    }

    public function edit(User $user): View
    {
        if (! (auth()->user()->can('edit admin-users') || auth()->id() === $user->id)) {
            abort(403);
        }
        $roles = Role::all();
        $permissionCategories = Permission::all()->groupBy('category');
        $statuses = Status::getStatuses();
        $objects = BObject::orderBy('code', 'desc')->get();

        return view('users.edit', compact('user', 'statuses', 'roles', 'permissionCategories', 'objects'));
    }

    public function update(User $user, UpdateUserRequest $request): RedirectResponse
    {
        if (! (auth()->user()->can('edit admin-users') || auth()->id() === $user->id)) {
            abort(403);
        }

        $this->userService->updateUser($user, $request->toArray());

        return redirect()->back();
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->destroyUser($user);
        return redirect()->back();
    }

    public function login(User $user): RedirectResponse
    {
        Auth::login($user);
        return redirect()->back();
    }
}
