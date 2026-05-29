<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\MonthlyFee;
use Carbon\Carbon;

class MonthlyFeeService
{
    /**
     * Generate monthly fees for an enrollment
     */
    public function generateMonthlyFees(Enrollment $enrollment): void
    {
        $course = $enrollment->course;
        $startDate = $enrollment->enrolled_at ?? now();
        $durationMonths = $course->duration_months ?? 6;
        
        // Calculate monthly amount
        $monthlyAmount = $enrollment->fees_total / $durationMonths;
        
        // Generate fees for each month
        for ($i = 0; $i < $durationMonths; $i++) {
            $feeDate = $startDate->copy()->addMonths($i);
            
            // Check if fee already exists for this month
            $existingFee = MonthlyFee::where([
                'enrollment_id' => $enrollment->id,
                'year' => $feeDate->year,
                'month' => $feeDate->month,
            ])->first();
            
            if (!$existingFee) {
                MonthlyFee::create([
                    'tenant_id' => $enrollment->tenant_id,
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $enrollment->student_id,
                    'year' => $feeDate->year,
                    'month' => $feeDate->month,
                    'amount' => round($monthlyAmount, 2),
                    'status' => $this->determineInitialStatus($feeDate),
                ]);
            }
        }
    }
    
    /**
     * Determine initial status based on date
     */
    private function determineInitialStatus(Carbon $date): string
    {
        $now = now();
        
        // If month is in the past, mark as overdue
        if ($date->year < $now->year || 
            ($date->year === $now->year && $date->month < $now->month)) {
            return 'overdue';
        }
        
        return 'pending';
    }
    
    /**
     * Update overdue fees
     */
    public function updateOverdueStatus(): void
    {
        $now = now();
        
        MonthlyFee::where('status', 'pending')
            ->where(function ($query) use ($now) {
                $query->where('year', '<', $now->year)
                    ->orWhere(function ($q) use ($now) {
                        $q->where('year', $now->year)
                          ->where('month', '<', $now->month);
                    });
            })
            ->update(['status' => 'overdue']);
    }
    
    /**
     * Get total pending amount for a student
     */
    public function getPendingAmount(int $studentId): float
    {
        return MonthlyFee::where('student_id', $studentId)
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('amount');
    }
    
    /**
     * Get overdue fees for a student
     */
    public function getOverdueFees(int $studentId)
    {
        return MonthlyFee::where('student_id', $studentId)
            ->where('status', 'overdue')
            ->with('enrollment.course')
            ->get();
    }
}
