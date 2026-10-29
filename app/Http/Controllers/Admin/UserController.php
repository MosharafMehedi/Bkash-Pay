<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    public function __construct(protected AuditLogService $audit) {}

    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($role = $request->query('role')) {
            $query->role($role);
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:8|confirmed',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'city'        => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'roles'       => 'required|array|min:1',
            'roles.*'     => 'exists:roles,name',
            'is_active'   => 'nullable|boolean',
        ]);

        try {
            $user = User::create([
                'name'        => $data['name'],
                'email'       => $data['email'],
                'password'    => Hash::make($data['password']),
                'phone'       => $data['phone'] ?? null,
                'address'     => $data['address'] ?? null,
                'city'        => $data['city'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'is_active'   => $request->boolean('is_active', true),
                'created_by'  => auth()->id(),
            ]);

            $user->syncRoles($data['roles']);

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $this->audit->logAction(
                "User created: {$user->name} ({$user->email})",
                $user,
                ['new' => $user->only(['name', 'email', 'is_active'])]
            );

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully with roles.');
        } catch (\Throwable $e) {
            Log::error('User create failed: ' . $e->getMessage());
            return back()->with('error', 'Could not create user.')->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'    => 'nullable|string|min:8|confirmed',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'city'        => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'roles'       => 'required|array|min:1',
            'roles.*'     => 'exists:roles,name',
            'is_active'   => 'nullable|boolean',
        ]);

        $isSelf = $user->id === auth()->id();

        try {
            $user->update([
                'name'        => $data['name'],
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'address'     => $data['address'] ?? null,
                'city'        => $data['city'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'is_active'   => $request->boolean('is_active', true),
                'updated_by'  => auth()->id(),
            ]);

            if (! empty($data['password'])) {
                $user->update(['password' => Hash::make($data['password'])]);
            }

            if (! $isSelf) {
                $user->syncRoles($data['roles']);
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->route('admin.users.index')
                ->with('success', $isSelf ? 'Profile updated (roles not changed for self).' : 'User updated successfully.');
        } catch (\Throwable $e) {
            Log::error('User update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update user.')->withInput();
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin user.');
        }

        try {
            $name = $user->name;
            $email = $user->email;
            $id = $user->id;

            $user->delete();

            $this->audit->logAction("User deleted: {$name} ({$email}, ID: {$id})");

            return back()->with('success', 'User deleted.');
        } catch (\Throwable $e) {
            Log::error('User delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete user.');
        }
    }

    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        try {
            $user->update(['is_active' => ! $user->is_active]);

            $this->audit->logAction(
                "User status toggled: {$user->name} → " . ($user->is_active ? 'Active' : 'Inactive'),
                $user
            );

            return back()->with('success', 'User status updated.');
        } catch (\Throwable $e) {
            Log::error('User toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update status.');
        }
    }
}