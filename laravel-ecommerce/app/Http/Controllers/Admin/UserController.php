<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status; 

        $users = User::when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($status, function($query) use ($status) {
                $query->where('is_blocked', $status == 'blocked' ? 1 : 0);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.users.index', compact('users', 'search', 'status'));
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
