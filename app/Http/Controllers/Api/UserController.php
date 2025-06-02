<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $user = User::create($validated);
        $user->assignRole('user');

        return response()->json($user, 201);
    }

    public function show(User $user):JsonResponse
    {
        $user->load('roles', 'discounts');
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());

        return response()->json($user);
    }
    public function updatePicture(Request $request) {
        $currentUser = auth()->user();
        $user = User::find($currentUser->id);
        if (!$currentUser->hasRole('admin') && $currentUser->id !== $user->id) {

            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $imageName = $this->storeImage($request);
        $user->update(['image' => $imageName]);

        if (request()->expectsJson()) {
            return response()->json($user);
        }

        return view('hello');
    }
    private function storeImage(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $file = $request->file('image');
        $name = $file->hashName();

        $image = $file->storeAs('images', $name, 'public');

        return $name;
    }
    public function destroy($id)
    {
        User::destroy($id);

        return response()->json(['message' => 'User deleted successfully']);
    }
}
