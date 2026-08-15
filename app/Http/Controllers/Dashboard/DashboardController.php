<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->getStats();

        return view('dashboard.index', compact('stats'));
    }

    private function getStats()
    {
        $today = now()->format('Y-m-d');
        $startOfWeek = now()->startOfWeek()->format('Y-m-d');
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');

        // Basic counts
        $totalCaregivers = Caregiver::count();
        $totalPatients = Patient::count();
        $patientsOnWard = Patient::where('patient_status', 'on_ward')->count();
        $patientsDischarged = Patient::where('patient_status', 'discharged')->count();
        $totalUsers = User::count();

        // Today's attendance
        $todayAttendance = Attendance::whereDate('date', $today)->count();

        // Payments
        $paymentsToday = Payment::whereDate('payment_date', $today)->sum('amount_paid');
        $paymentsWeek = Payment::whereDate('payment_date', '>=', $startOfWeek)->sum('amount_paid');
        $paymentsMonth = Payment::whereMonth('payment_date', now()->month)->sum('amount_paid');

        // Expenses
        $expensesToday = Expense::whereDate('expense_date', $today)->sum('amount');
        $expensesWeek = Expense::whereDate('expense_date', '>=', $startOfWeek)->sum('amount');
        $expensesMonth = Expense::whereMonth('expense_date', now()->month)->sum('amount');

        // User role distribution for pie chart
        $roleDistribution = User::select('role_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('role_type')
            ->pluck('count', 'role_type')
            ->toArray();

        // 6-month chart data
        $chartLabels = [];
        $chartPayments = [];
        $chartExpenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->format('M Y');
            $chartPayments[] = (float) Payment::whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount_paid');
            $chartExpenses[] = (float) Expense::whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');
        }

        // Recent records
        $recentPatients = Patient::latest()->take(5)->get();
        $recentAttendances = Attendance::with(['caregiver', 'patient'])->latest()->take(5)->get();

        return [
            'total_caregivers' => $totalCaregivers,
            'total_patients' => $totalPatients,
            'patients_on_ward' => $patientsOnWard,
            'patients_discharged' => $patientsDischarged,
            'total_users' => $totalUsers,
            'today_attendance' => $todayAttendance,

            'payments' => [
                'today' => $paymentsToday,
                'week' => $paymentsWeek,
                'month' => $paymentsMonth,
            ],

            'expenses' => [
                'today' => $expensesToday,
                'week' => $expensesWeek,
                'month' => $expensesMonth,
            ],

            'role_distribution' => $roleDistribution,

            'chart' => [
                'labels' => $chartLabels,
                'payments' => $chartPayments,
                'expenses' => $chartExpenses,
            ],

            'recent_patients' => $recentPatients,
            'recent_attendances' => $recentAttendances,
        ];
    }
}
