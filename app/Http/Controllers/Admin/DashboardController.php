<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants'     => Tenant::count(),
            'active_tenants'    => Tenant::where('status', 'active')->count(),
            'pending_tenants'   => Tenant::where('status', 'pending')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'total_students'    => User::withoutGlobalScopes()->whereHas('roles', fn($q) => $q->where('name', 'student'))->count(),
            'total_teachers'    => User::withoutGlobalScopes()->whereHas('roles', fn($q) => $q->where('name', 'teacher'))->count(),
            'total_courses'     => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'active_domains'    => Tenant::whereNotNull('custom_domain')->where('custom_domain', '!=', '')->count(),
            'expiring_soon'     => Tenant::where('status', 'active')
                                    ->whereNotNull('expires_at')
                                    ->where('expires_at', '<=', now()->addDays(30))
                                    ->where('expires_at', '>=', now())
                                    ->count(),
            'new_tenants_month' => Tenant::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
        ];

        $recentTenants = Tenant::withCount(['users', 'courses'])
            ->latest()->take(8)->get();

        $expiringTenants = Tenant::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30))
            ->where('expires_at', '>=', now())
            ->orderBy('expires_at')
            ->take(5)
            ->get();

        // Monthly tenant signups for the last 6 months
        $monthlyGrowth = Tenant::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTenants', 'expiringTenants', 'monthlyGrowth'));
    }
}
