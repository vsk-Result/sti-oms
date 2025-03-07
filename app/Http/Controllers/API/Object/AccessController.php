<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        $objectUser = [];
        $objects = BObject::with('users')->orderBy('code')->get();

        foreach ($objects as $object) {
            $objectUser[$object->id] = [];

            foreach ($object->users as $user) {
                $objectUser[$object->id][] = $user->id;
            }
        }

        $userObject = [];
        $users = User::with('objects')->orderBy('name')->get();

        foreach ($users as $user) {
            $userObject[$user->id] = [];

            foreach ($user->objects as $object) {
                $userObject[$user->id][] = $object->id;
            }
        }

        $info = [compact('objectUser', 'userObject')];

        return response()->json(compact('info'));
    }
}
