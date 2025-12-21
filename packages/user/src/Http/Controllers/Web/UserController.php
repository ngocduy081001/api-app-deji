<?php

namespace Vendor\User\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Vendor\User\Http\Requests\StoreUserRequest;
use Vendor\User\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(config('user.per_page', 20));

        return view('user::admin.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('user::admin.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Người dùng đã được tạo thành công.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('user::admin.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('user::admin.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // Only update password if provided
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Người dùng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        $currentUser = auth()->user();
        if ($currentUser && $user->id === $currentUser->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Bạn không thể xóa chính mình.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Người dùng đã được xóa thành công.');
    }
}
