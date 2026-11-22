<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\AvatarStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        User::create($request->safe()->only(['username', 'full_name', 'email', 'password']));

        return redirect()->route('users.index')->with('success', 'User account created. You can now edit it to add a profile picture.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user, AvatarStorage $avatars)
    {
        $data = $request->safe()->only(['username', 'full_name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $oldAvatar = $user->avatar;
        $newAvatar = null;

        if ($request->hasFile('avatar')) {
            $newAvatar = $avatars->store($request->file('avatar'));
            $data['avatar'] = $newAvatar;
        }

        try {
            $user->update($data);
        } catch (\Throwable $exception) {
            if ($newAvatar) {
                Storage::disk('public')->delete($newAvatar);
            }

            throw $exception;
        }

        if ($newAvatar && $oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()->route('users.index')->with('success', 'User account updated.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 403, 'Use Profile to delete your own account after confirming your password.');
        $avatar = $user->avatar;
        $user->delete();

        if ($avatar) {
            Storage::disk('public')->delete($avatar);
        }

        return redirect()->route('users.index')->with('success', 'User account deleted.');
    }

    public function avatar(string $filename)
    {
        $path = 'avatars/'.$filename;
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}
