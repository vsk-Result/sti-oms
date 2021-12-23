<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): View
    {
        $objects = BObject::with('users')->orderBy('code')->get();
        return view('objects.users.index', compact('objects'));
    }

    public function edit(BObject $object): View
    {
        $users = User::orderBy('name')->get();
        return view('objects.users.edit', compact('object', 'users'));
    }

    public function update(BObject $object, Request $request): RedirectResponse
    {
        $object->users()->sync($request->get('user_id', []));
        return redirect()->route('objects.users.index');
    }
}
