<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Mail\Message;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Tenant;
use App\Models\TenantRegistration;

class RegisterController extends Controller
{
    // ──────────────────────────────────────────────
    // STEP 1: Coaching Info
    // ──────────────────────────────────────────────
    public function showTenantRegistration()
    {
        return view('auth.register.index');
    }

    public function step1(Request $request)
    {
        $request->validate([
            'coaching_name' => 'required|string|max:255',
            'subdomain'     => 'required|string|max:50|alpha_dash|unique:tenants,subdomain',
            'coaching_type' => 'required|string|max:100',
            'tagline'       => 'nullable|string|max:255',
            'website'       => 'nullable|url|max:255',
        ], [
            'subdomain.unique' => 'This subdomain is already taken. Please choose another.',
            'subdomain.alpha_dash' => 'Subdomain may only contain letters, numbers, dashes and underscores.',
        ]);

        $reg = TenantRegistration::startNew();
        $reg->mergeData([
            'coaching_name' => $request->coaching_name,
            'subdomain'     => strtolower($request->subdomain),
            'coaching_type' => $request->coaching_type,
            'tagline'       => $request->tagline,
            'website'       => $request->website,
        ]);
        $reg->step = 2;
        $reg->save();

        return redirect()->route('register.step2', ['token' => $reg->token]);
    }

    // ──────────────────────────────────────────────
    // STEP 2: Contact Details
    // ──────────────────────────────────────────────
    public function showStep2(Request $request, string $token)
    {
        $reg = $this->findReg($token, 2);
        return view('auth.register.step2', compact('reg'));
    }

    public function step2(Request $request, string $token)
    {
        $reg = $this->findReg($token, 2);

        $request->validate([
            'email'   => 'required|email|max:255|unique:tenants,email',
            'phone'   => 'required|string|max:20',
            'phone_alt' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'city'    => 'required|string|max:100',
            'state'   => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
        ], [
            'email.unique' => 'A coaching centre with this email is already registered.',
        ]);

        $reg->mergeData([
            'email'     => $request->email,
            'phone'     => $request->phone,
            'phone_alt' => $request->phone_alt,
            'address'   => $request->address,
            'city'      => $request->city,
            'state'     => $request->state,
            'pincode'   => $request->pincode,
        ]);
        $reg->step = 3;
        $reg->save();

        return redirect()->route('register.step3', ['token' => $reg->token]);
    }

    // ──────────────────────────────────────────────
    // STEP 3: Admin Account
    // ──────────────────────────────────────────────
    public function showStep3(Request $request, string $token)
    {
        $reg = $this->findReg($token, 3);
        return view('auth.register.step3', compact('reg'));
    }

    public function step3(Request $request, string $token)
    {
        $reg = $this->findReg($token, 3);

        $request->validate([
            'admin_name'  => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_phone' => 'required|string|max:20',
            'password'    => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ], [
            'admin_email.unique' => 'This email is already in use.',
            'password.regex'     => 'Password must include uppercase, lowercase, and a number.',
        ]);

        $reg->mergeData([
            'admin_name'  => $request->admin_name,
            'admin_email' => $request->admin_email,
            'admin_phone' => $request->admin_phone,
            'password'    => Hash::make($request->password),
        ]);
        $reg->step = 4;
        $reg->save();

        return redirect()->route('register.review', ['token' => $reg->token]);
    }

    // ──────────────────────────────────────────────
    // STEP 4: Review & Confirm
    // ──────────────────────────────────────────────
    public function showReview(Request $request, string $token)
    {
        $reg = $this->findReg($token, 4);
        return view('auth.register.review', compact('reg'));
    }

    public function confirmReview(Request $request, string $token)
    {
        $reg = $this->findReg($token, 4);

        // Send OTP to admin email
        $otp = $reg->generateOtp();
        [$fromAddr, $fromName] = $this->applySystemMailConfig();

        try {
            Mail::send([], [], function (Message $msg) use ($reg, $otp, $fromAddr, $fromName) {
                $msg->to($reg->get('admin_email'), $reg->get('admin_name'))
                    ->from($fromAddr, $fromName)
                    ->subject('Verify your email – BT Guru Registration')
                    ->html(view('emails.registration_otp', [
                        'otp'          => $otp,
                        'coachingName' => $reg->get('coaching_name'),
                        'name'         => $reg->get('admin_name'),
                    ])->render());
            });
        } catch (\Throwable $e) {
            \Log::warning('OTP email failed: ' . $e->getMessage());
        }

        $reg->step = 5;
        $reg->save();

        return redirect()->route('register.verify', ['token' => $reg->token])
            ->with('success', 'OTP sent to ' . $reg->get('admin_email'));
    }

    // ──────────────────────────────────────────────
    // STEP 5: Email Verification
    // ──────────────────────────────────────────────
    public function showVerify(Request $request, string $token)
    {
        $reg = $this->findReg($token, 5);
        return view('auth.register.verify', compact('reg'));
    }

    public function verify(Request $request, string $token)
    {
        $reg = $this->findReg($token, 5);

        $request->validate(['otp' => 'required|string|size:6']);

        if (!$reg->isOtpValid($request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        // ── Create Tenant ──────────────────────────
        $tenant = Tenant::create([
            'coaching_name' => $reg->get('coaching_name'),
            'slug'          => Str::slug($reg->get('coaching_name')),
            'subdomain'     => $reg->get('subdomain'),
            'email'         => $reg->get('email'),
            'phone'         => $reg->get('phone'),
            'address'       => trim(implode(', ', array_filter([
                $reg->get('address'),
                $reg->get('city'),
                $reg->get('state'),
                $reg->get('pincode'),
            ]))),
            'status'        => 'pending',
            'settings'      => [
                'tagline'       => $reg->get('tagline'),
                'website'       => $reg->get('website'),
                'coaching_type' => $reg->get('coaching_type'),
                'phone_alt'     => $reg->get('phone_alt'),
                'city'          => $reg->get('city'),
                'state'         => $reg->get('state'),
                'pincode'       => $reg->get('pincode'),
            ],
        ]);

        // ── Create Admin User ──────────────────────
        $admin = User::create([
            'tenant_id'         => $tenant->id,
            'name'              => $reg->get('admin_name'),
            'email'             => $reg->get('admin_email'),
            'phone'             => $reg->get('admin_phone'),
            'password'          => $reg->get('password'),
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('tenant_admin');

        // ── Send welcome email ─────────────────────
        [$fromAddr, $fromName] = $this->applySystemMailConfig();
        try {
            Mail::send([], [], function (Message $msg) use ($admin, $tenant, $fromAddr, $fromName) {
                $msg->to($admin->email, $admin->name)
                    ->from($fromAddr, $fromName)
                    ->subject('Welcome to BT Guru – ' . $tenant->coaching_name)
                    ->html(view('emails.registration_welcome', [
                        'admin'  => $admin,
                        'tenant' => $tenant,
                    ])->render());
            });
        } catch (\Throwable $e) {
            \Log::warning('Welcome email failed: ' . $e->getMessage());
        }

        // ── Cleanup ────────────────────────────────
        $reg->delete();

        return redirect()->route('home')
            ->with('success', 'Registration complete! Your coaching centre is under review. You will receive an email once activated.');
    }

    // Resend OTP
    public function resendOtp(Request $request, string $token)
    {
        $reg = $this->findReg($token, 5);
        $otp = $reg->generateOtp();
        [$fromAddr, $fromName] = $this->applySystemMailConfig();

        try {
            Mail::send([], [], function (Message $msg) use ($reg, $otp, $fromAddr, $fromName) {
                $msg->to($reg->get('admin_email'), $reg->get('admin_name'))
                    ->from($fromAddr, $fromName)
                    ->subject('Your new OTP – BT Guru Registration')
                    ->html(view('emails.registration_otp', [
                        'otp'          => $otp,
                        'coachingName' => $reg->get('coaching_name'),
                        'name'         => $reg->get('admin_name'),
                    ])->render());
            });
        } catch (\Throwable $e) {
            \Log::warning('Resend OTP email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'New OTP sent to ' . $reg->get('admin_email'));
    }

    // Check subdomain availability (AJAX)
    public function checkSubdomain(Request $request)
    {
        $subdomain = strtolower(trim($request->query('subdomain', '')));
        if (strlen($subdomain) < 3) {
            return response()->json(['available' => false, 'message' => 'Too short']);
        }
        $exists = Tenant::where('subdomain', $subdomain)->exists();
        return response()->json([
            'available' => !$exists,
            'message'   => $exists ? 'Already taken' : 'Available!',
        ]);
    }

    // ──────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────
    private function findReg(string $token, int $minStep): TenantRegistration
    {
        $reg = TenantRegistration::findByToken($token);
        if (!$reg || $reg->step < $minStep) {
            abort(404, 'Registration session not found or expired.');
        }
        return $reg;
    }

    /**
     * Apply system SMTP settings from DB to Laravel's runtime mail config.
     * Returns [from_address, from_name] for use in closures.
     */
    private function applySystemMailConfig(): array
    {
        if (!SystemSetting::isMailConfigured()) {
            return [
                config('mail.from.address', 'noreply@btguru.in'),
                config('mail.from.name', 'BT Guru'),
            ];
        }

        $cfg = SystemSetting::mailConfig();

        Config::set('mail.default', $cfg['driver']);
        Config::set('mail.mailers.smtp.host', $cfg['host']);
        Config::set('mail.mailers.smtp.port', $cfg['port']);
        Config::set('mail.mailers.smtp.username', $cfg['username']);
        Config::set('mail.mailers.smtp.password', $cfg['password']);
        Config::set('mail.mailers.smtp.encryption', $cfg['encryption'] ?: null);
        Config::set('mail.from.address', $cfg['from_address']);
        Config::set('mail.from.name', $cfg['from_name']);

        return [$cfg['from_address'], $cfg['from_name']];
    }

    public function showStudentRegistration()
    {
        $currentTenant = app('current_tenant');
        return view('auth.student-register', compact('currentTenant'));
    }

    public function studentRegister(Request $request)
    {
        $currentTenant = app('current_tenant');
        
        if (!$currentTenant) {
            return back()->with('error', 'Tenant not found.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,NULL,id,tenant_id,' . $currentTenant->id,
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create student
        $student = User::create([
            'tenant_id' => $currentTenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $student->assignRole('student');

        // Create enrollment if course selected
        if ($request->course_id) {
            $course = \App\Models\Course::find($request->course_id);
            if ($course) {
                \App\Models\Enrollment::create([
                    'tenant_id' => $currentTenant->id,
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'payment_status' => 'pending',
                    'enrollment_status' => 'pending',
                    'fees_total' => $course->fees,
                ]);
            }
        }

        return redirect()->route('student.login')
            ->with('success', 'Registration successful! You can now login.');
    }
}
