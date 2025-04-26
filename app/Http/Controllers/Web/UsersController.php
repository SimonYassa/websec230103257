<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use App\Mail\ResetPasswordEmail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except([
            'showLoginForm', 
            'login', 
            'showRegistrationForm', 
            'register', 
            'verify', 
            'showVerificationNotice',
            'showForgotPasswordForm',
            'sendResetLinkEmail',
            'showResetForm',
            'resetPassword'
        ]);
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
    
        // Find the user
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput();
        }
    
        // IMPORTANT: Check if email is verified
        if (!$user->email_verified_at) {
            Log::info('Login attempt for unverified user: ' . $user->email);
            return back()->withErrors([
                'email' => 'Your email is not verified. Please check your email for a verification link.',
            ])->withInput();
        }
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('products_home'));
        }
    
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
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
     * Verify email address
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        Log::info('Verification attempt with token: ' . $request->token);
        
        if (!$request->has('token')) {
            Log::error('No token provided');
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid verification link.']);
        }
        
        try {
            // Try to decrypt the token
            $decryptedData = json_decode(Crypt::decryptString($request->token), true);
            Log::info('Decrypted token data: ', $decryptedData);
            
            // Find the user
            $user = User::find($decryptedData['id']);
            
            if (!$user) {
                Log::error('User not found for ID: ' . $decryptedData['id']);
                return redirect()->route('login')
                    ->withErrors(['email' => 'Invalid verification link.']);
            }
            
            // Check if already verified
            if ($user->email_verified_at) {
                Log::info('User already verified: ' . $user->email);
                return redirect()->route('login')
                    ->with('status', 'Your email is already verified. You can now log in.');
            }
            
            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();
            
            Log::info('User verified successfully: ' . $user->email);
            
            // Redirect to login with success message
            return redirect()->route('login')
                ->with('status', 'Your email has been verified! You can now log in.');
        } catch (\Exception $e) {
            Log::error('Verification error: ' . $e->getMessage());
            
            // Try alternative method using password_reset_tokens table
            $tokenRecord = DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->first();
            
            if (!$tokenRecord) {
                Log::error('Token not found in password_reset_tokens table');
                return redirect()->route('login')
                    ->withErrors(['email' => 'Invalid or expired verification link.']);
            }
            
            // Find the user by email
            $user = User::where('email', $tokenRecord->email)->first();
            
            if (!$user) {
                Log::error('User not found for email: ' . $tokenRecord->email);
                return redirect()->route('login')
                    ->withErrors(['email' => 'User not found.']);
            }
            
            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();
            
            // Delete the token
            DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->delete();
            
            Log::info('User verified successfully (alternative method): ' . $user->email);
            
            // Redirect to login with success message
            return redirect()->route('login')
                ->with('status', 'Your email has been verified! You can now log in.');
        }
    }

    /**
     * Show verification notice page
     *
     * @return \Illuminate\Http\Response
     */
    public function showVerificationNotice()
    {
        return view('auth.verify');
    }

    /**
     * Resend verification email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resendVerification(Request $request)
    {
        // Must be logged in
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // Check if already verified
        if ($user->email_verified_at) {
            return redirect()->route('products_home')
                ->with('status', 'Your email is already verified.');
        }
        
        // Generate verification token
        $token = Str::random(60);
        
        // Store token in database
        DB::table('password_reset_tokens')
            ->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );
        
        // Generate verification link
        $link = route("verify", ['token' => $token]);
        
        // Send verification email
        Mail::to($user->email)->send(new VerificationEmail($link, $user->name));
        
        return redirect()->route('verification.notice')
            ->with('status', 'Verification link has been resent to your email address.');
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
            // email_verified_at is null by default
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

        // Generate verification token
        $token = Str::random(60);
        
        // Store token in database
        DB::table('password_reset_tokens')
            ->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );
        
        // Generate verification link
        $link = route("verify", ['token' => $token]);
        
        Log::info('Generated verification link for ' . $user->email . ': ' . $link);
        
        // Send verification email
        Mail::to($user->email)->send(new VerificationEmail($link, $user->name));

        // Redirect to verification notice page
        return redirect()->route('verification.notice')
            ->with('status', 'Registration successful! Please check your email to verify your account.');
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

    /**
     * Show the forgot password form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showForgotPasswordForm()
    {
        return view('users.forgot_password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Always show success message even if email doesn't exist (security best practice)
        if (!$user) {
            \Illuminate\Support\Facades\Log::info('Password reset requested for non-existent email: ' . $request->email);
            return back()->with('status', 'If your email exists in our system, you will receive a password reset link shortly.');
        }

        // Generate token
        $token = \Illuminate\Support\Str::random(60);
        
        // Store token in database
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );
        
        // Generate reset link
        $resetLink = route('password.reset', ['token' => $token]);
        
        \Illuminate\Support\Facades\Log::info('Password reset link generated for ' . $user->email . ': ' . $resetLink);
        
        // Send email
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new ResetPasswordEmail($resetLink, $user->name));
        
        return back()->with('status', 'If your email exists in our system, you will receive a password reset link shortly.');
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request)
    {
        $token = $request->token;
        
        // Verify token exists
        $tokenRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();
        
        if (!$tokenRecord) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Invalid or expired password reset link.']);
        }
        
        return view('users.reset_password', ['token' => $token, 'email' => $tokenRecord->email]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Verify token exists and matches email
        $tokenRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();
        
        if (!$tokenRecord) {
            return back()->withErrors(['email' => 'Invalid or expired password reset link.']);
        }
        
        // Check if token is expired (tokens valid for 60 minutes)
        $tokenCreatedAt = \Carbon\Carbon::parse($tokenRecord->created_at);
        if ($tokenCreatedAt->diffInMinutes(now()) > 60) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('token', $request->token)->delete();
            return back()->withErrors(['email' => 'Password reset link has expired.']);
        }
        
        // Find user and update password
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }
        
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();
        
        // Delete the token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('token', $request->token)->delete();
        
        \Illuminate\Support\Facades\Log::info('Password reset successful for user: ' . $user->email);
        
        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. You can now log in with your new password.');
    }
}
