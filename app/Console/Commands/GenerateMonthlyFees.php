<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use App\Services\MonthlyFeeService;

class GenerateMonthlyFees extends Command
{
    protected $signature = 'fees:generate {--enrollment=} {--tenant=}';
    protected $description = 'Generate monthly fees for enrollments';

    public function handle(): void
    {
        $service = new MonthlyFeeService();
        
        if ($enrollmentId = $this->option('enrollment')) {
            // Generate for specific enrollment
            $enrollment = Enrollment::find($enrollmentId);
            if ($enrollment) {
                $service->generateMonthlyFees($enrollment);
                $this->info("Monthly fees generated for enrollment #{$enrollmentId}");
            } else {
                $this->error("Enrollment not found");
            }
            return;
        }
        
        // Generate for all active enrollments
        $enrollments = Enrollment::where('enrollment_status', 'active')
            ->whereHas('monthlyFees', function ($q) {
                $q->where('status', 'pending');
            }, '<', 1) // No monthly fees yet
            ->get();
        
        $count = 0;
        foreach ($enrollments as $enrollment) {
            $service->generateMonthlyFees($enrollment);
            $count++;
        }
        
        $this->info("Generated monthly fees for {$count} enrollments");
    }
}
