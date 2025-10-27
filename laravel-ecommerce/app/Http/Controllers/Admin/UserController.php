<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('admin.users.index', compact('users'));
    }

    // Toggle block/unblock
    public function toggleBlock(User $user)
    {
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        return redirect()->back()->with('status', 'User status updated successfully!');
    }

    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,customer',
        ]);

        $user->role = $request->role;
        $user->save();

        return back()->with('status', "Role updated successfully for {$user->name}!");
    }

}
