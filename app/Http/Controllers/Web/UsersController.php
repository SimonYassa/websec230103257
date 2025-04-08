<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['showLoginForm', 'login', 'showRegistrationForm', 'register']);
        // Remove role middleware and implement checks in individual methods
        // $this->middleware('role:Employee')->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'addCredit']);
        // $this->middleware('role:Admin')->only(['createEmployee']);
    }

    /**
     * Display the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('users.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('products_home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Display the registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('users.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'credit' => 0,
        ]);

        // Assign Customer role to new user
        $customerRole = Role::where('name', 'Customer')->first();
        if ($customerRole) {
            $user->assignRole($customerRole);
        } else {
            // Create Customer role if it doesn't exist
            $customerRole = Role::create(['name' => 'Customer']);
            $user->assignRole($customerRole);
        }

        Auth::login($user);

        return redirect()->route('products_home');
    }

    /**
     * Display the user's profile form.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        $purchases = $user->purchases()->with('product')->latest()->get();
        
        return view('users.profile', [
            'user' => $user,
            'purchases' => $purchases
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('status', 'Profile updated successfully!');
    }

    /**
     * Show the form for editing the user's password.
     *
     * @return \Illuminate\Http\Response
     */
    public function editPassword()
    {
        $user = Auth::user();
        return view('users.edit_password', compact('user'));
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password updated successfully.');
    }

    /**
     * Display a listing of users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        // Get filter parameter
        $filter = $request->query('filter', 'all');
        
        // If user is an Admin, they can see all users or filter by role
        if (Auth::user()->hasRole('Admin')) {
            if ($filter === 'customers') {
                $users = User::role('Customer')->get();
                $activeFilter = 'customers';
            } elseif ($filter === 'employees') {
                $users = User::role('Employee')->get();
                $activeFilter = 'employees';
            } elseif ($filter === 'admins') {
                $users = User::role('Admin')->get();
                $activeFilter = 'admins';
            } else {
                $users = User::all();
                $activeFilter = 'all';
            }
        } else {
            // Regular employees can only see customers
            $users = User::role('Customer')->get();
            $activeFilter = 'customers';
        }
        
        // Debug information
        if ($users->isEmpty()) {
            // If no users found, show a message
            return view('users.list', compact('users', 'activeFilter'))->with('info', 'No users found with the selected filter.');
        }
        
        return view('users.list', compact('users', 'activeFilter'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // No one can create new customers now
        return redirect()->route('users_index')->with('error', 'Adding new customers is disabled.');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // No one can create new customers now
        return redirect()->route('users_index')->with('error', 'Adding new customers is disabled.');
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function createEmployee()
    {
        // Check if user has Admin role
        if (!Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        return view('users.create_employee');
    }

    /**
     * Store a newly created employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeEmployee(Request $request)
    {
        // Check if user has Admin role
        if (!Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'credit' => 0,
        ]);

        // Assign Employee role to new user
        $employeeRole = Role::where('name', 'Employee')->first();
        if ($employeeRole) {
            $user->assignRole($employeeRole);
        } else {
            // Create Employee role if it doesn't exist
            $employeeRole = Role::create(['name' => 'Employee']);
            $user->assignRole($employeeRole);
        }

        return redirect()->route('users_index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // Check if user has Employee role or is viewing their own profile
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin') && Auth::id() !== $user->id) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        $purchases = $user->purchases()->with('product')->latest()->get();
        return view('users.show', compact('user', 'purchases'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return redirect()->route('users_index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Show form to add credit to a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function showAddCredit(User $user)
    {
        // Check if user has Employee role and is not an Admin
        if (Auth::user()->hasRole('Admin')) {
            return redirect()->route('users_index')->with('error', 'Admins are not allowed to add credit to users.');
        }
        
        if (!Auth::user()->hasRole('Employee')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        // Only allow adding credit to customers
        if (!$user->hasRole('Customer')) {
            return redirect()->route('users_index')->with('error', 'Credit can only be added to customers.');
        }
        
        return view('users.add_credit', compact('user'));
    }

    /**
     * Add credit to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function addCredit(Request $request, User $user)
    {
        // Check if user has Employee role and is not an Admin
        if (Auth::user()->hasRole('Admin')) {
            return redirect()->route('users_index')->with('error', 'Admins are not allowed to add credit to users.');
        }
        
        if (!Auth::user()->hasRole('Employee')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        // Only allow adding credit to customers
        if (!$user->hasRole('Customer')) {
            return redirect()->route('users_index')->with('error', 'Credit can only be added to customers.');
        }
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user->addCredit($request->amount);

        return redirect()->route('users_index')
            ->with('success', 'Credit added successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        $user->delete();

        return redirect()->route('users_index')
            ->with('success', 'User deleted successfully.');
    }
}
