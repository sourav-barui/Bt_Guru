<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PaymentController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Admin\DomainController as AdminDomainController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\SubscriptionPlanController as AdminSubscriptionPlanController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\BTLiveSettingsController;

// Tenant Controllers
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\CourseController as TenantCourseController;
use App\Http\Controllers\Tenant\TeacherController as TenantTeacherController;
use App\Http\Controllers\Tenant\StudentController as TenantStudentController;
use App\Http\Controllers\Tenant\EnrollmentController as TenantEnrollmentController;
use App\Http\Controllers\Tenant\NoticeController as TenantNoticeController;
use App\Http\Controllers\Tenant\CurriculumController;
use App\Http\Controllers\Tenant\SubscriptionController as TenantSubscriptionController;
use App\Http\Controllers\Tenant\PaymentRequestController as TenantPaymentRequestController;
use App\Http\Controllers\Tenant\SettingsController as TenantSettingsController;
use App\Http\Controllers\Tenant\LiveClassController as TenantLiveClassController;
use App\Http\Controllers\Tenant\ExamController;
use App\Http\Controllers\Tenant\BookController as TenantBookController;
use App\Http\Controllers\Tenant\ProfileController as TenantProfileController;

// Teacher Controllers
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;

// Student Controllers
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\LiveClassController as StudentLiveClassController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\NotificationController as StudentNotificationController;
use App\Http\Controllers\Student\BookController as StudentBookController;

// BTLive Controllers
use App\Http\Controllers\BTLiveController;
use App\Http\Controllers\BTLiveRecordingController;

/*
|--------------------------------------------------------------------------
| Detect Subdomain and Route Accordingly
|--------------------------------------------------------------------------
*/

$host = Request::getHost();
$centralDomain = config('app.central_domain');
$adminSubdomain = config('app.admin_subdomain');

// Check if this is admin subdomain
$isAdminSubdomain = str_starts_with($host, $adminSubdomain . '.');

// Check if this is central domain only (no subdomain)
$isCentralDomain = $host === $centralDomain;

/*
|--------------------------------------------------------------------------
| Central Domain Routes (Landing Page, Tenant Registration)
|--------------------------------------------------------------------------
*/

if ($isCentralDomain) {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [HomeController::class, 'about'])->name('about');
    Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

    // Tenant Registration Wizard
    Route::get('/register', [RegisterController::class, 'showTenantRegistration'])->name('tenant.register');
    Route::post('/register/step1', [RegisterController::class, 'step1'])->name('register.step1');
    Route::get('/register/step2/{token}', [RegisterController::class, 'showStep2'])->name('register.step2');
    Route::post('/register/step2/{token}', [RegisterController::class, 'step2']);
    Route::get('/register/step3/{token}', [RegisterController::class, 'showStep3'])->name('register.step3');
    Route::post('/register/step3/{token}', [RegisterController::class, 'step3']);
    Route::get('/register/review/{token}', [RegisterController::class, 'showReview'])->name('register.review');
    Route::post('/register/review/{token}', [RegisterController::class, 'confirmReview']);
    Route::get('/register/verify/{token}', [RegisterController::class, 'showVerify'])->name('register.verify');
    Route::post('/register/verify/{token}', [RegisterController::class, 'verify'])->name('register.verify.post');
    Route::post('/register/resend-otp/{token}', [RegisterController::class, 'resendOtp'])->name('register.resend_otp');
    Route::get('/register/check-subdomain', [RegisterController::class, 'checkSubdomain'])->name('register.check_subdomain');
}

/*
|--------------------------------------------------------------------------
| Admin Subdomain Routes (Super Admin Panel)
|--------------------------------------------------------------------------
*/

if ($isAdminSubdomain) {
    // Admin Auth
    Route::get('/login', [LoginController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'adminLogin']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware(['auth', 'super.admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Tenant Management
        Route::resource('tenants', AdminTenantController::class)->names([
            'index' => 'admin.tenants.index',
            'create' => 'admin.tenants.create',
            'store' => 'admin.tenants.store',
            'show' => 'admin.tenants.show',
            'edit' => 'admin.tenants.edit',
            'update' => 'admin.tenants.update',
            'destroy' => 'admin.tenants.destroy',
        ]);
        Route::post('tenants/{tenant}/suspend', [AdminTenantController::class, 'suspend'])->name('admin.tenants.suspend');
        Route::post('tenants/{tenant}/activate', [AdminTenantController::class, 'activate'])->name('admin.tenants.activate');

        // Domain Management
        Route::resource('domains', AdminDomainController::class)->names([
            'index' => 'admin.domains.index',
            'create' => 'admin.domains.create',
            'store' => 'admin.domains.store',
            'edit' => 'admin.domains.edit',
            'update' => 'admin.domains.update',
            'destroy' => 'admin.domains.destroy',
        ]);
        Route::post('domains/{tenant}/verify', [AdminDomainController::class, 'verify'])->name('admin.domains.verify');

        // System Settings
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
        Route::post('/settings/test-mail', [AdminSettingsController::class, 'testMail'])->name('admin.settings.test_mail');
        Route::post('/settings/diagnose-mail', [AdminSettingsController::class, 'diagnoseMail'])->name('admin.settings.diagnose_mail');
        
        // BTLive Settings
        Route::get('/btlive-settings', [BTLiveSettingsController::class, 'index'])->name('admin.btlive.settings');
        Route::post('/btlive-settings', [BTLiveSettingsController::class, 'update'])->name('admin.btlive.settings.update');
        Route::post('/btlive-settings/cleanup', [BTLiveSettingsController::class, 'cleanup'])->name('admin.btlive.cleanup');
        Route::post('/btlive-settings/test-s3', [BTLiveSettingsController::class, 'testS3'])->name('admin.btlive.test-s3');

        // Subscription Plans
        Route::prefix('subscription-plans')->name('admin.subscription_plans.')->group(function () {
            Route::get('/', [AdminSubscriptionPlanController::class, 'index'])->name('index');
            Route::get('/create', [AdminSubscriptionPlanController::class, 'create'])->name('create');
            Route::post('/', [AdminSubscriptionPlanController::class, 'store'])->name('store');
            Route::get('/{plan}', [AdminSubscriptionPlanController::class, 'show'])->name('show');
            Route::get('/{plan}/edit', [AdminSubscriptionPlanController::class, 'edit'])->name('edit');
            Route::put('/{plan}', [AdminSubscriptionPlanController::class, 'update'])->name('update');
            Route::delete('/{plan}', [AdminSubscriptionPlanController::class, 'destroy'])->name('destroy');
        });

        // Coupons
        Route::prefix('coupons')->name('admin.coupons.')->group(function () {
            Route::get('/', [AdminCouponController::class, 'index'])->name('index');
            Route::get('/create', [AdminCouponController::class, 'create'])->name('create');
            Route::post('/', [AdminCouponController::class, 'store'])->name('store');
            Route::get('/{coupon}', [AdminCouponController::class, 'show'])->name('show');
            Route::get('/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('edit');
            Route::put('/{coupon}', [AdminCouponController::class, 'update'])->name('update');
            Route::delete('/{coupon}', [AdminCouponController::class, 'destroy'])->name('destroy');
        });

        // Subscriptions (Tenant Subscriptions)
        Route::prefix('subscriptions')->name('admin.subscriptions.')->group(function () {
            Route::get('/', [AdminSubscriptionController::class, 'index'])->name('index');
            Route::get('/{subscription}', [AdminSubscriptionController::class, 'show'])->name('show');
            Route::get('/{subscription}/edit', [AdminSubscriptionController::class, 'edit'])->name('edit');
            Route::put('/{subscription}', [AdminSubscriptionController::class, 'update'])->name('update');
            Route::post('/{subscription}/cancel', [AdminSubscriptionController::class, 'cancel'])->name('cancel');
            Route::post('/{subscription}/activate', [AdminSubscriptionController::class, 'activate'])->name('activate');
        });

        // Payments
        Route::prefix('payments')->name('admin.payments.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/verify', [PaymentController::class, 'verifyPayment'])->name('verify');
        });
    });
}

/*
|--------------------------------------------------------------------------
| Tenant Subdomain Routes
|--------------------------------------------------------------------------
*/

if (!$isAdminSubdomain && !$isCentralDomain) {
    
    // Public Tenant Routes
    Route::get('/', function () {
        $tenant = app('current_tenant');
        return view('tenant.landing', compact('tenant'));
    })->name('tenant.home');

    // PWA Manifest (dynamic per tenant)
    Route::get('/manifest.json', [App\Http\Controllers\Tenant\PwaManifestController::class, 'manifest'])->name('tenant.manifest');

    // Tenant Login (for Tenant Admin and Teachers)
    Route::get('/login', [LoginController::class, 'showTenantLogin'])->name('tenant.login');
    Route::post('/login', [LoginController::class, 'tenantLogin']);

    // Student Login
    Route::get('/student/login', [LoginController::class, 'showStudentLogin'])->name('student.login');
    Route::post('/student/login', [LoginController::class, 'studentLogin']);

    // Session Conflict
    Route::get('/student/login-conflict', [LoginController::class, 'showSessionConflict'])->name('student.login.conflict');

    // Student Forgot Password with OTP
    Route::get('/student/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('student.password.request');
    Route::post('/student/forgot-password', [LoginController::class, 'sendPasswordResetOtp'])->name('student.password.otp.send');
    Route::get('/student/verify-otp', [LoginController::class, 'showVerifyOtpForm'])->name('student.password.otp.verify');
    Route::post('/student/verify-otp', [LoginController::class, 'verifyOtp'])->name('student.password.otp.check');
    Route::get('/student/reset-password', [LoginController::class, 'showResetPasswordFormOtp'])->name('student.password.reset.otp');
    Route::post('/student/reset-password', [LoginController::class, 'resetPasswordOtp'])->name('student.password.update.otp');

    // Tenant Forgot Password with OTP (for Tenant Admin and Teachers)
    Route::get('/forgot-password', [LoginController::class, 'showTenantForgotPasswordForm'])->name('tenant.password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendTenantPasswordResetOtp'])->name('tenant.password.otp.send');
    Route::get('/verify-otp', [LoginController::class, 'showTenantVerifyOtpForm'])->name('tenant.password.otp.verify');
    Route::post('/verify-otp', [LoginController::class, 'verifyTenantOtp'])->name('tenant.password.otp.check');
    Route::get('/reset-password', [LoginController::class, 'showTenantResetPasswordFormOtp'])->name('tenant.password.reset.otp');
    Route::post('/reset-password', [LoginController::class, 'resetTenantPasswordOtp'])->name('tenant.password.update.otp');

    // Student Registration
    Route::get('/student/register', [RegisterController::class, 'showStudentRegistration'])->name('student.register');
    Route::post('/student/register', [RegisterController::class, 'studentRegister']);

    // Shared Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Universal Dashboard Redirect (redirects to appropriate dashboard based on role)
    Route::middleware(['auth'])->get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('tenant_admin')) {
            return redirect()->route('tenant.dashboard');
        } elseif ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }
        return redirect('/');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Tenant Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:tenant_admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');

        // Courses
        Route::resource('courses', TenantCourseController::class)->names([
            'index' => 'tenant.courses.index',
            'create' => 'tenant.courses.create',
            'store' => 'tenant.courses.store',
            'show' => 'tenant.courses.show',
            'edit' => 'tenant.courses.edit',
            'update' => 'tenant.courses.update',
            'destroy' => 'tenant.courses.destroy',
        ]);

        // Curriculum Management
        Route::prefix('courses/{course}/curriculum')->group(function () {
            Route::get('/', [CurriculumController::class, 'index'])->name('tenant.curriculum.index');
            
            // Curriculum Sections
            Route::get('/curricula/create', [CurriculumController::class, 'createCurriculum'])->name('tenant.curriculum.curricula.create');
            Route::post('/curricula', [CurriculumController::class, 'storeCurriculum'])->name('tenant.curriculum.curricula.store');
            Route::get('/curricula/{curriculum}/edit', [CurriculumController::class, 'editCurriculum'])->name('tenant.curriculum.curricula.edit');
            Route::put('/curricula/{curriculum}', [CurriculumController::class, 'updateCurriculum'])->name('tenant.curriculum.curricula.update');
            Route::delete('/curricula/{curriculum}', [CurriculumController::class, 'destroyCurriculum'])->name('tenant.curriculum.curricula.destroy');
            
            // Subjects
            Route::get('/curricula/{curriculum}/subjects/create', [CurriculumController::class, 'createSubject'])->name('tenant.curriculum.subjects.create');
            Route::post('/curricula/{curriculum}/subjects', [CurriculumController::class, 'storeSubject'])->name('tenant.curriculum.subjects.store');
            Route::get('/subjects/{subject}/edit', [CurriculumController::class, 'editSubject'])->name('tenant.curriculum.subjects.edit');
            Route::put('/subjects/{subject}', [CurriculumController::class, 'updateSubject'])->name('tenant.curriculum.subjects.update');
            Route::delete('/subjects/{subject}', [CurriculumController::class, 'destroySubject'])->name('tenant.curriculum.subjects.destroy');
            
            // Chapters
            Route::get('/subjects/{subject}/chapters/create', [CurriculumController::class, 'createChapter'])->name('tenant.curriculum.chapters.create');
            Route::post('/subjects/{subject}/chapters', [CurriculumController::class, 'storeChapter'])->name('tenant.curriculum.chapters.store');
            Route::get('/chapters/{chapter}/edit', [CurriculumController::class, 'editChapter'])->name('tenant.curriculum.chapters.edit');
            Route::put('/chapters/{chapter}', [CurriculumController::class, 'updateChapter'])->name('tenant.curriculum.chapters.update');
            Route::delete('/chapters/{chapter}', [CurriculumController::class, 'destroyChapter'])->name('tenant.curriculum.chapters.destroy');
            
            // Lessons
            Route::get('/chapters/{chapter}/lessons/create', [CurriculumController::class, 'createLesson'])->name('tenant.curriculum.lessons.create');
            Route::post('/chapters/{chapter}/lessons', [CurriculumController::class, 'storeLesson'])->name('tenant.curriculum.lessons.store');
            Route::get('/lessons/{lesson}/edit', [CurriculumController::class, 'editLesson'])->name('tenant.curriculum.lessons.edit');
            Route::put('/lessons/{lesson}', [CurriculumController::class, 'updateLesson'])->name('tenant.curriculum.lessons.update');
            Route::delete('/lessons/{lesson}', [CurriculumController::class, 'destroyLesson'])->name('tenant.curriculum.lessons.destroy');
            
            // Content & Notes
            Route::post('/content', [CurriculumController::class, 'storeContent'])->name('tenant.curriculum.content.store');
            Route::delete('/content/{content}', [CurriculumController::class, 'destroyContent'])->name('tenant.curriculum.content.destroy');
            Route::post('/notes', [CurriculumController::class, 'storeNote'])->name('tenant.curriculum.notes.store');
            Route::delete('/notes/{note}', [CurriculumController::class, 'destroyNote'])->name('tenant.curriculum.notes.destroy');
        });

        // Live Classes
        Route::prefix('courses/{course}/live-classes')->name('tenant.live_classes.')->group(function () {
            Route::get('/', [TenantLiveClassController::class, 'index'])->name('index');
            Route::get('/create', [TenantLiveClassController::class, 'create'])->name('create');
            Route::post('/', [TenantLiveClassController::class, 'store'])->name('store');
            Route::get('/{liveClass}/edit', [TenantLiveClassController::class, 'edit'])->name('edit');
            Route::put('/{liveClass}', [TenantLiveClassController::class, 'update'])->name('update');
            Route::delete('/{liveClass}', [TenantLiveClassController::class, 'destroy'])->name('destroy');
            Route::post('/{liveClass}/mark-live', [TenantLiveClassController::class, 'markLive'])->name('markLive');
            Route::post('/{liveClass}/end-live', [TenantLiveClassController::class, 'endLive'])->name('endLive');
            Route::post('/{liveClass}/mark-completed', [TenantLiveClassController::class, 'markCompleted'])->name('markCompleted');
            Route::post('/{liveClass}/upload-video', [TenantLiveClassController::class, 'uploadVideo'])->name('uploadVideo');
        });

        // BTLive Routes
        Route::prefix('btlive')->name('btlive.')->group(function () {
            Route::get('/create/{course}', [BTLiveController::class, 'create'])->name('create');
            Route::post('/store/{course}', [BTLiveController::class, 'store'])->name('store');
            Route::get('/{liveClass}/room', [BTLiveController::class, 'teacherRoom'])->name('teacher_room');
            Route::get('/{liveClass}/attendance', [BTLiveController::class, 'attendance'])->name('attendance');
            Route::post('/{liveClass}/end', [BTLiveController::class, 'endMeeting'])->name('end_meeting');
            Route::post('/{liveClass}/kick-participant', [BTLiveController::class, 'kickParticipant'])->name('kick_participant');
            Route::post('/{liveClass}/mute-all', [BTLiveController::class, 'muteAll'])->name('mute_all');
            Route::get('/{liveClass}/live-stats', [BTLiveController::class, 'liveStats'])->name('live_stats');
            Route::post('/{liveClass}/convert', [BTLiveController::class, 'convertToBTLive'])->name('convert');
            Route::post('/{liveClass}/recording-webhook', [BTLiveController::class, 'recordingWebhook'])->name('recording_webhook');
            Route::post('/{liveClass}/attendance-webhook', [BTLiveController::class, 'attendanceWebhook'])->name('attendance_webhook');
            
            // Recording Management
            Route::get('/{liveClass}/recordings', [BTLiveRecordingController::class, 'index'])->name('recordings.index');
            Route::post('/recordings/{recording}/approve', [BTLiveRecordingController::class, 'approve'])->name('recordings.approve');
            Route::post('/recordings/{recording}/reject', [BTLiveRecordingController::class, 'reject'])->name('recordings.reject');
            Route::get('/recordings/{recording}/download', [BTLiveRecordingController::class, 'download'])->name('recordings.download');
            Route::get('/recordings', [BTLiveRecordingController::class, 'adminIndex'])->name('recordings.admin');
        });

        // Exams
        Route::prefix('courses/{course}/exams')->name('tenant.exams.')->group(function () {
            Route::get('/', [ExamController::class, 'index'])->name('index');
            Route::get('/create', [ExamController::class, 'create'])->name('create');
            Route::post('/', [ExamController::class, 'store'])->name('store');
            Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
            Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
            Route::post('/{exam}/publish', [ExamController::class, 'publish'])->name('publish');
            
            // Sections
            Route::post('/{exam}/sections', [ExamController::class, 'storeSection'])->name('sections.store');
            
            // Questions
            Route::get('/{exam}/questions/create', [ExamController::class, 'createQuestions'])->name('questions.create');
            Route::post('/{exam}/questions', [ExamController::class, 'storeQuestions'])->name('questions.store');
            Route::post('/{exam}/questions/import', [ExamController::class, 'importQuestions'])->name('questions.import');
            Route::get('/{exam}/questions/template', [ExamController::class, 'downloadTemplate'])->name('questions.template');
        });

        // Teachers
        Route::resource('teachers', TenantTeacherController::class)->names([
            'index' => 'tenant.teachers.index',
            'create' => 'tenant.teachers.create',
            'store' => 'tenant.teachers.store',
            'show' => 'tenant.teachers.show',
            'edit' => 'tenant.teachers.edit',
            'update' => 'tenant.teachers.update',
            'destroy' => 'tenant.teachers.destroy',
        ]);

        // Students
        Route::resource('students', TenantStudentController::class)->names([
            'index' => 'tenant.students.index',
            'create' => 'tenant.students.create',
            'store' => 'tenant.students.store',
            'show' => 'tenant.students.show',
            'edit' => 'tenant.students.edit',
            'update' => 'tenant.students.update',
            'destroy' => 'tenant.students.destroy',
        ]);
        Route::post('/students/{student}/logout-all-devices', [TenantStudentController::class, 'logoutFromAllDevices'])->name('tenant.students.logout_all');

        // Enrollments
        Route::resource('enrollments', TenantEnrollmentController::class)->names([
            'index' => 'tenant.enrollments.index',
            'create' => 'tenant.enrollments.create',
            'store' => 'tenant.enrollments.store',
            'show' => 'tenant.enrollments.show',
            'edit' => 'tenant.enrollments.edit',
            'update' => 'tenant.enrollments.update',
            'destroy' => 'tenant.enrollments.destroy',
        ]);
        Route::post('enrollments/{enrollment}/approve', [TenantEnrollmentController::class, 'approve'])->name('tenant.enrollments.approve');
        Route::post('enrollments/{enrollment}/activate', [TenantEnrollmentController::class, 'activate'])->name('tenant.enrollments.activate');
        Route::post('enrollments/{enrollment}/payment', [TenantEnrollmentController::class, 'addPayment'])->name('tenant.enrollments.addPayment');

        // Notices
        Route::resource('notices', TenantNoticeController::class)->names([
            'index' => 'tenant.notices.index',
            'create' => 'tenant.notices.create',
            'store' => 'tenant.notices.store',
            'show' => 'tenant.notices.show',
            'edit' => 'tenant.notices.edit',
            'update' => 'tenant.notices.update',
            'destroy' => 'tenant.notices.destroy',
        ]);

        // Settings
        Route::get('/settings', [TenantSettingsController::class, 'index'])->name('tenant.settings');
        Route::post('/settings', [TenantSettingsController::class, 'update'])->name('tenant.settings.update');

        // Payment Requests Management
        Route::prefix('payment-requests')->name('tenant.payments.')->group(function () {
            Route::get('/', [TenantPaymentRequestController::class, 'index'])->name('index');
            Route::get('/{payment}', [TenantPaymentRequestController::class, 'show'])->name('show');
            Route::post('/{payment}/approve', [TenantPaymentRequestController::class, 'approve'])->name('approve');
            Route::post('/{payment}/reject', [TenantPaymentRequestController::class, 'reject'])->name('reject');
            Route::post('/{payment}/enroll', [TenantPaymentRequestController::class, 'enroll'])->name('enroll');
            Route::post('/{payment}/rewind', [TenantPaymentRequestController::class, 'rewind'])->name('rewind');
        });

        // Books
        Route::resource('books', TenantBookController::class)->names([
            'index' => 'tenant.books.index',
            'create' => 'tenant.books.create',
            'store' => 'tenant.books.store',
            'show' => 'tenant.books.show',
            'edit' => 'tenant.books.edit',
            'update' => 'tenant.books.update',
            'destroy' => 'tenant.books.destroy',
        ]);
        Route::get('/books/{book}/download', [TenantBookController::class, 'downloadPdf'])->name('tenant.books.download');
        Route::get('/book-orders', [TenantBookController::class, 'orders'])->name('tenant.books.orders');
        Route::patch('/book-orders/{order}/status', [TenantBookController::class, 'updateOrderStatus'])->name('tenant.books.orders.status');

        // Subscription Management (monthly courses)
        Route::prefix('subscriptions')->name('tenant.subscriptions.')->group(function () {
            Route::get('/', [TenantSubscriptionController::class, 'index'])->name('index');
            Route::get('/create', [TenantSubscriptionController::class, 'create'])->name('create');
            Route::post('/', [TenantSubscriptionController::class, 'store'])->name('store');
            Route::patch('/{subscription}/status', [TenantSubscriptionController::class, 'updateStatus'])->name('updateStatus');
            Route::delete('/{subscription}', [TenantSubscriptionController::class, 'destroy'])->name('destroy');
            Route::get('/course-info/{course}', [TenantSubscriptionController::class, 'getCourseInfo'])->name('courseInfo');

            // Platform Subscription
            Route::get('/platform', [TenantSubscriptionController::class, 'platformPlans'])->name('platform_plans');
            Route::post('/apply-coupon', [TenantSubscriptionController::class, 'applyCoupon'])->name('apply_coupon');
            Route::post('/subscribe', [TenantSubscriptionController::class, 'subscribe'])->name('subscribe');
            Route::get('/current', [TenantSubscriptionController::class, 'currentSubscription'])->name('current');

            // Payments
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/select-method/{subscription}', [PaymentController::class, 'selectMethod'])->name('select_method');
                Route::post('/razorpay/initiate/{subscription}', [PaymentController::class, 'initiateRazorpay'])->name('razorpay_initiate');
                Route::post('/razorpay/verify', [PaymentController::class, 'verifyRazorpay'])->name('razorpay_verify');
                Route::get('/upi-qr/{subscription}', [PaymentController::class, 'showUpiQr'])->name('upi_qr');
                Route::post('/upi-qr/{subscription}', [PaymentController::class, 'submitUpiPayment'])->name('upi_submit');
                Route::get('/manual/{subscription}', [PaymentController::class, 'showManualPayment'])->name('manual');
                Route::post('/manual/{subscription}', [PaymentController::class, 'submitManualPayment'])->name('manual_submit');
            });
        });

        // Profile Management
        Route::get('/profile', [TenantProfileController::class, 'index'])->name('tenant.profile');
        Route::post('/profile', [TenantProfileController::class, 'update'])->name('tenant.profile.update');
        Route::post('/profile/password', [TenantProfileController::class, 'changePassword'])->name('tenant.profile.password');
    });

    /*
    |--------------------------------------------------------------------------
    | Teacher Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:teacher|tenant_admin'])->prefix('teacher')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
        
        // Teacher Courses
        Route::get('/courses', [TeacherDashboardController::class, 'myCourses'])->name('teacher.courses');
        Route::get('/courses/{course}', [TeacherDashboardController::class, 'showCourse'])->name('teacher.courses.show');
        
        // Teacher Live Classes
        Route::prefix('courses/{course}/live-classes')->name('teacher.live_classes.')->group(function () {
            Route::get('/', [TenantLiveClassController::class, 'index'])->name('index');
            Route::get('/create', [TenantLiveClassController::class, 'create'])->name('create');
            Route::post('/', [TenantLiveClassController::class, 'store'])->name('store');
            Route::get('/{liveClass}/edit', [TenantLiveClassController::class, 'edit'])->name('edit');
            Route::put('/{liveClass}', [TenantLiveClassController::class, 'update'])->name('update');
            Route::delete('/{liveClass}', [TenantLiveClassController::class, 'destroy'])->name('destroy');
            Route::post('/{liveClass}/mark-live', [TenantLiveClassController::class, 'markLive'])->name('markLive');
            Route::post('/{liveClass}/end-live', [TenantLiveClassController::class, 'endLive'])->name('endLive');
            Route::post('/{liveClass}/mark-completed', [TenantLiveClassController::class, 'markCompleted'])->name('markCompleted');
            Route::post('/{liveClass}/upload-video', [TenantLiveClassController::class, 'uploadVideo'])->name('uploadVideo');
        });

    // Teacher Exams
        Route::prefix('courses/{course}/exams')->name('teacher.exams.')->group(function () {
            Route::get('/', [TeacherExamController::class, 'index'])->name('index');
            Route::get('/create', [TeacherExamController::class, 'create'])->name('create');
            Route::post('/', [TeacherExamController::class, 'store'])->name('store');
            Route::get('/{exam}', [TeacherExamController::class, 'show'])->name('show');
            Route::delete('/{exam}', [TeacherExamController::class, 'destroy'])->name('destroy');
            Route::post('/{exam}/publish', [TeacherExamController::class, 'publish'])->name('publish');
            Route::post('/{exam}/sections', [TeacherExamController::class, 'storeSection'])->name('sections.store');
            Route::get('/{exam}/questions/create', [TeacherExamController::class, 'createQuestions'])->name('questions.create');
            Route::post('/{exam}/questions', [TeacherExamController::class, 'storeQuestions'])->name('questions.store');
            Route::post('/{exam}/questions/import', [TeacherExamController::class, 'importQuestions'])->name('questions.import');
            Route::get('/{exam}/questions/template', [TeacherExamController::class, 'downloadTemplate'])->name('questions.template');
        });

    // Teacher Curriculum Management (same as admin)
        Route::prefix('courses/{course}/curriculum')->name('teacher.curriculum.')->group(function () {
            Route::get('/', [CurriculumController::class, 'index'])->name('index');
            
            // Curriculum Sections
            Route::get('/curricula/create', [CurriculumController::class, 'createCurriculum'])->name('createCurriculum');
            Route::post('/curricula', [CurriculumController::class, 'storeCurriculum'])->name('storeCurriculum');
            Route::get('/curricula/{curriculum}/edit', [CurriculumController::class, 'editCurriculum'])->name('editCurriculum');
            Route::put('/curricula/{curriculum}', [CurriculumController::class, 'updateCurriculum'])->name('updateCurriculum');
            Route::delete('/curricula/{curriculum}', [CurriculumController::class, 'destroyCurriculum'])->name('destroyCurriculum');
            
            // Subjects
            Route::get('/{curriculum}/subjects/create', [CurriculumController::class, 'createSubject'])->name('createSubject');
            Route::post('/{curriculum}/subjects', [CurriculumController::class, 'storeSubject'])->name('storeSubject');
            Route::get('/subjects/{subject}/edit', [CurriculumController::class, 'editSubject'])->name('editSubject');
            Route::put('/subjects/{subject}', [CurriculumController::class, 'updateSubject'])->name('updateSubject');
            Route::delete('/subjects/{subject}', [CurriculumController::class, 'destroySubject'])->name('destroySubject');
            
            // Chapters
            Route::get('/{subject}/chapters/create', [CurriculumController::class, 'createChapter'])->name('createChapter');
            Route::post('/{subject}/chapters', [CurriculumController::class, 'storeChapter'])->name('storeChapter');
            Route::get('/chapters/{chapter}/edit', [CurriculumController::class, 'editChapter'])->name('editChapter');
            Route::put('/chapters/{chapter}', [CurriculumController::class, 'updateChapter'])->name('updateChapter');
            Route::delete('/chapters/{chapter}', [CurriculumController::class, 'destroyChapter'])->name('destroyChapter');
            
            // Lessons
            Route::get('/{chapter}/lessons/create', [CurriculumController::class, 'createLesson'])->name('createLesson');
            Route::post('/{chapter}/lessons', [CurriculumController::class, 'storeLesson'])->name('storeLesson');
            Route::get('/lessons/{lesson}/edit', [CurriculumController::class, 'editLesson'])->name('editLesson');
            Route::put('/lessons/{lesson}', [CurriculumController::class, 'updateLesson'])->name('updateLesson');
            Route::delete('/lessons/{lesson}', [CurriculumController::class, 'destroyLesson'])->name('destroyLesson');
            
            // Content & Notes
            Route::post('/content', [CurriculumController::class, 'storeContent'])->name('storeContent');
            Route::delete('/content/{content}', [CurriculumController::class, 'destroyContent'])->name('destroyContent');
            Route::post('/notes', [CurriculumController::class, 'storeNote'])->name('storeNote');
            Route::delete('/notes/{note}', [CurriculumController::class, 'destroyNote'])->name('destroyNote');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:student', 'single.session'])->prefix('student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::get('/courses', [StudentDashboardController::class, 'myCourses'])->name('student.courses');
        Route::get('/courses/all', [StudentDashboardController::class, 'allCourses'])->name('student.courses.all');
        
        // Hierarchical Course Content Routes
        Route::get('/courses/{course}/access', [StudentDashboardController::class, 'accessCourse'])->name('student.courses.access');
        Route::get('/courses/{course}/subjects/{subject}', [StudentDashboardController::class, 'viewSubject'])->name('student.subject.show');
        Route::get('/courses/{course}/subjects/{subject}/chapters/{chapter}', [StudentDashboardController::class, 'viewChapter'])->name('student.chapter.show');
        Route::get('/courses/{course}/subjects/{subject}/chapters/{chapter}/lessons/{lesson}', [StudentDashboardController::class, 'viewLesson'])->name('student.lesson.show');
        
        Route::get('/fees', [StudentDashboardController::class, 'feeStatus'])->name('student.fees');

        // Monthly Fees Management
        Route::get('/monthly-fees', [StudentDashboardController::class, 'monthlyFees'])->name('student.monthly-fees');
        Route::get('/monthly-fees/pay-all', [StudentDashboardController::class, 'payAllMonthlyFees'])->name('student.fees.pay-all');
        Route::post('/monthly-fees/pay-all', [StudentDashboardController::class, 'processAllMonthlyFees'])->name('student.fees.process-all');
        Route::get('/monthly-fees/enrollment/{enrollment}/pay', [StudentDashboardController::class, 'payCourseFees'])->name('student.fees.pay-course');
        Route::post('/monthly-fees/enrollment/{enrollment}/pay', [StudentDashboardController::class, 'processCourseFees'])->name('student.fees.process-course');
        Route::get('/monthly-fees/{fee}/pay', [StudentDashboardController::class, 'payMonthlyFee'])->name('student.fees.pay-month');
        Route::post('/monthly-fees/{fee}/pay', [StudentDashboardController::class, 'processMonthlyFeePayment'])->name('student.fees.process-month');

        // Payment Requests
        Route::get('/payments', [StudentPaymentController::class, 'index'])->name('student.payments.index');
        Route::get('/payments/create', [StudentPaymentController::class, 'create'])->name('student.payments.create');
        Route::post('/payments', [StudentPaymentController::class, 'store'])->name('student.payments.store');

        // Live Classes
        Route::get('/live-classes', [StudentLiveClassController::class, 'index'])->name('student.live_classes.index');
        
        // BTLive Student
        Route::get('/btlive/test', function() {
            return 'TEST ROUTE WORKS - Student ID: ' . (Auth::id() ?? 'not logged in');
        });
        Route::get('/btlive/{liveClass}/join-simple', function($liveClassId) {
            $class = \App\Models\LiveClass::find($liveClassId);
            if (!$class) return 'Class not found: ' . $liveClassId;
            return 'SIMPLE JOIN WORKS - Class: ' . $class->title . ' (ID: ' . $class->id . ')';
        });
        // TEMPORARY: Working simple join that renders the view
        Route::get('/btlive/{id}/join-new', function($id) {
            $liveClass = \App\Models\LiveClass::findOrFail($id);
            $student = Auth::user();
            $jwt = '';
            $jitsiConfig = ['domain' => 'meet.jit.si', 'roomName' => $liveClass->btlive_room_name ?? 'test-room'];
            return view('btlive.student_room_simple', compact('liveClass', 'jwt', 'jitsiConfig'));
        })->name('student.btlive.join.new');
        Route::get('/btlive/{liveClass}/join', [BTLiveController::class, 'studentRoom'])->name('student.btlive.join');
        Route::post('/btlive/{liveClass}/leave', [BTLiveController::class, 'studentLeave'])->name('student.btlive.leave');

        // Exams
        Route::get('/exams', [StudentExamController::class, 'index'])->name('student.exams.index');
        Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('student.exams.show');
        Route::post('/exams/{exam}/start', [StudentExamController::class, 'startAttempt'])->name('student.exams.start');
        Route::get('/exams/{exam}/attempt/{attempt}', [StudentExamController::class, 'attempt'])->name('student.exams.attempt');
        Route::post('/exams/{exam}/attempt/{attempt}/answer', [StudentExamController::class, 'saveAnswer'])->name('student.exams.save_answer');
        Route::post('/exams/{exam}/attempt/{attempt}/submit', [StudentExamController::class, 'submit'])->name('student.exams.submit');
        Route::get('/exams/{exam}/attempt/{attempt}/results', [StudentExamController::class, 'results'])->name('student.exams.results');

        // Notifications
        Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('student.notifications.index');
        Route::get('/notifications/recent', [StudentNotificationController::class, 'recent'])->name('student.notifications.recent');
        Route::get('/notifications/unread-count', [StudentNotificationController::class, 'unreadCount'])->name('student.notifications.unread_count');
        Route::post('/notifications/{notification}/read', [StudentNotificationController::class, 'markRead'])->name('student.notifications.read');
        Route::post('/notifications/read-all', [StudentNotificationController::class, 'markAllRead'])->name('student.notifications.read_all');

        // Notices
        Route::get('/notices/{notice}', [StudentDashboardController::class, 'viewNotice'])->name('student.notices.show');

        // Notes (PDF Viewer)
        Route::get('/notes/{note}', [StudentDashboardController::class, 'viewNote'])->name('student.notes.show');

        // Profile Management
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('student.profile');
        Route::post('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('student.profile.update');
        Route::post('/profile/password', [App\Http\Controllers\Student\ProfileController::class, 'changePassword'])->name('student.profile.password');
        Route::post('/profile/delete', [App\Http\Controllers\Student\ProfileController::class, 'deleteAccount'])->name('student.profile.delete');

        // Book Store
        Route::get('/books', [StudentBookController::class, 'index'])->name('student.books.index');
        Route::get('/books/{book}', [StudentBookController::class, 'show'])->name('student.books.show');
        Route::get('/books/{book}/checkout', [StudentBookController::class, 'checkout'])->name('student.books.checkout');
        Route::post('/books/{book}/purchase', [StudentBookController::class, 'purchase'])->name('student.books.purchase');
        Route::post('/books/verify', [StudentBookController::class, 'verifyRazorpay'])->name('student.books.verify');
        Route::get('/books/orders/{order}/download', [StudentBookController::class, 'download'])->name('student.books.download');
        Route::get('/my-book-orders', [StudentBookController::class, 'myOrders'])->name('student.books.my_orders');

        // About Page
        Route::get('/about', [StudentDashboardController::class, 'about'])->name('student.about');
    });
}
