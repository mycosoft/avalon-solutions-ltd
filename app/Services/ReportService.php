<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Caregiver;
use App\Models\Expense;
use App\Models\Patient;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportService
{
    public function supportedTypes(): array
    {
        return [
            'financial'      => 'Financial Summary',
            'payments'       => 'Payments Report',
            'expenses'       => 'Expenses Report',
            'patients'       => 'Patients Report',
            'outstanding'    => 'Outstanding Balances',
            'caregivers'     => 'Caregivers Report',
            'attendance'     => 'Attendance Report',
            'attendance-general' => 'General Attendance (Ward Round)',
        ];
    }

    public function build(string $type, Request $request): array
    {
        return match ($type) {
            'financial' => $this->financial($request),
            'payments' => $this->payments($request),
            'expenses' => $this->expenses($request),
            'patients' => $this->patients($request),
            'outstanding' => $this->outstanding($request),
            'caregivers' => $this->caregivers($request),
            'attendance'         => $this->attendance($request),
            'attendance-general' => $this->attendanceGeneral($request),
            default => abort(404),
        };
    }

    public function filename(string $type, string $extension): string
    {
        $types = $this->supportedTypes();
        $label = strtolower(str_replace(' ', '-', $types[$type] ?? $type));

        return $label.'-'.now()->format('Y-m-d').'.'.$extension;
    }

    private function defaultPeriod(Request $request): array
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        return [$dateFrom, $dateTo];
    }

    private function periodLabel(string $dateFrom, string $dateTo): string
    {
        return Carbon::parse($dateFrom)->format('d M Y').' - '.Carbon::parse($dateTo)->format('d M Y');
    }

    private function money($value): string
    {
        return number_format((float) $value, 0);
    }

    private function baseReport(string $type, string $title, string $description, array $filters): array
    {
        return [
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'filters' => $filters,
            'totals' => [],
            'sections' => [],
            'generated_at' => now()->format('d M Y H:i'),
        ];
    }

    private function financial(Request $request): array
    {
        [$dateFrom, $dateTo] = $this->defaultPeriod($request);

        $report = $this->baseReport(
            'financial',
            'Financial Summary Report',
            'Income and expense overview for the selected period.',
            [
                ['label' => 'Period', 'value' => $this->periodLabel($dateFrom, $dateTo)],
            ]
        );

        $totalIncome = (float) Payment::whereBetween('payment_date', [$dateFrom, $dateTo])->sum('amount_paid');
        $totalExpenses = (float) Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        $net = $totalIncome - $totalExpenses;

        $report['totals'] = [
            ['label' => 'Total Income', 'value' => $this->money($totalIncome), 'class' => 'success'],
            ['label' => 'Total Expenses', 'value' => $this->money($totalExpenses), 'class' => 'danger'],
            ['label' => 'Net Income', 'value' => $this->money($net), 'class' => $net >= 0 ? 'primary' : 'warning'],
        ];

        $paymentsByMethod = Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount_paid) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $report['sections'][] = [
            'title' => 'Payments by Method',
            'headers' => ['Payment Method', 'Transactions', 'Total Amount'],
            'rows' => $paymentsByMethod->map(fn ($row) => [
                ucfirst(str_replace('_', ' ', $row->payment_method)),
                $row->count,
                $this->money($row->total),
            ])->all(),
            'empty' => $paymentsByMethod->isEmpty() ? 'No payments recorded in the selected period.' : null,
        ];

        $expensesByCategory = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->selectRaw('category, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $categories = Expense::categories();
        $report['sections'][] = [
            'title' => 'Expenses by Category',
            'headers' => ['Category', 'Transactions', 'Total Amount'],
            'rows' => $expensesByCategory->map(fn ($row) => [
                $categories[$row->category] ?? ucfirst($row->category),
                $row->count,
                $this->money($row->total),
            ])->all(),
            'empty' => $expensesByCategory->isEmpty() ? 'No expenses recorded in the selected period.' : null,
        ];

        $months = [];
        $cursor = Carbon::parse($dateFrom)->startOfMonth();
        $end = Carbon::parse($dateTo)->endOfMonth();
        while ($cursor->lte($end)) {
            $months[] = $cursor->copy();
            $cursor->addMonth();
        }

        $monthlyRows = [];
        foreach ($months as $month) {
            $payments = (float) Payment::whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount_paid');
            $expenses = (float) Expense::whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');
            $monthlyRows[] = [
                $month->format('M Y'),
                $this->money($payments),
                $this->money($expenses),
                $this->money($payments - $expenses),
            ];
        }

        $report['sections'][] = [
            'title' => 'Monthly Breakdown',
            'headers' => ['Month', 'Payments', 'Expenses', 'Net'],
            'rows' => $monthlyRows,
        ];

        return $report;
    }

    private function payments(Request $request): array
    {
        [$dateFrom, $dateTo] = $this->defaultPeriod($request);

        $query = Payment::with('patient')
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);

        if ($request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $report = $this->baseReport(
            'payments',
            'Payments Report',
            'Detailed payment records for the selected period.',
            $this->paymentFilters($request, $dateFrom, $dateTo, true)
        );

        $totalAmount = (float) $payments->sum('amount_paid');
        $report['totals'] = [
            ['label' => 'Transactions', 'value' => $payments->count(), 'class' => 'primary'],
            ['label' => 'Total Amount', 'value' => $this->money($totalAmount), 'class' => 'success'],
            ['label' => 'Partial Payments', 'value' => $payments->where('payment_type', 'partial')->count(), 'class' => 'warning'],
        ];

        $report['sections'][] = [
            'title' => 'Payment Records',
            'headers' => ['Date', 'Patient', 'Payee', 'Amount', 'Days', 'Method', 'Type', 'Balance', 'Recorded By'],
            'rows' => $payments->map(fn ($payment) => [
                $payment->payment_date->format('Y-m-d'),
                $payment->patient->name ?? 'N/A',
                $payment->payee_name,
                $this->money($payment->amount_paid),
                $payment->days_paid,
                ucfirst(str_replace('_', ' ', $payment->payment_method)),
                ucfirst($payment->payment_type),
                $this->money($payment->balance),
                $payment->recorded_by ?? 'N/A',
            ])->all(),
            'empty' => $payments->isEmpty() ? 'No payments found for the selected filters.' : null,
        ];

        return $report;
    }

    private function paymentFilters(Request $request, string $dateFrom, string $dateTo, bool $includePatient = true): array
    {
        $filters = [
            ['label' => 'Period', 'value' => $this->periodLabel($dateFrom, $dateTo)],
        ];

        if ($includePatient && $request->patient_id) {
            $filters[] = ['label' => 'Patient', 'value' => Patient::find($request->patient_id)?->name ?? 'N/A'];
        }

        if ($request->payment_method) {
            $filters[] = ['label' => 'Method', 'value' => ucfirst(str_replace('_', ' ', $request->payment_method))];
        }

        return $filters;
    }

    private function expenses(Request $request): array
    {
        [$dateFrom, $dateTo] = $this->defaultPeriod($request);

        $query = Expense::whereBetween('expense_date', [$dateFrom, $dateTo]);

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $categories = Expense::categories();

        $report = $this->baseReport(
            'expenses',
            'Expenses Report',
            'Detailed expense records for the selected period.',
            [
                ['label' => 'Period', 'value' => $this->periodLabel($dateFrom, $dateTo)],
                ...($request->category ? [['label' => 'Category', 'value' => $categories[$request->category] ?? ucfirst($request->category)]] : []),
            ]
        );

        $totalAmount = (float) $expenses->sum('amount');
        $report['totals'] = [
            ['label' => 'Transactions', 'value' => $expenses->count(), 'class' => 'primary'],
            ['label' => 'Total Amount', 'value' => $this->money($totalAmount), 'class' => 'danger'],
        ];

        $report['sections'][] = [
            'title' => 'Expense Records',
            'headers' => ['Date', 'Description', 'Category', 'Amount', 'Recorded By'],
            'rows' => $expenses->map(fn ($expense) => [
                $expense->expense_date->format('Y-m-d'),
                $expense->description,
                $categories[$expense->category] ?? ucfirst($expense->category),
                $this->money($expense->amount),
                $expense->recorded_by ?? 'N/A',
            ])->all(),
            'empty' => $expenses->isEmpty() ? 'No expenses found for the selected filters.' : null,
        ];

        return $report;
    }

    private function patients(Request $request): array
    {
        $query = Patient::with('payments');

        if ($request->patient_status) {
            $query->where('patient_status', $request->patient_status);
        }

        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        if ($request->ward) {
            $query->where('ward', 'like', '%'.$request->ward.'%');
        }

        if ($request->date_from) {
            $query->whereDate('date_of_admission', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date_of_admission', '<=', $request->date_to);
        }

        $patients = $query->orderBy('date_of_admission', 'desc')->get();

        $statusLabels = [
            'on_ward' => 'On Ward',
            'transferred' => 'Transferred',
            'discharged' => 'Discharged',
        ];

        $filters = [];
        if ($request->patient_status) {
            $filters[] = ['label' => 'Status', 'value' => $statusLabels[$request->patient_status] ?? ucfirst($request->patient_status)];
        }
        if ($request->gender) {
            $filters[] = ['label' => 'Gender', 'value' => ucfirst($request->gender)];
        }
        if ($request->ward) {
            $filters[] = ['label' => 'Ward', 'value' => $request->ward];
        }
        if ($request->date_from || $request->date_to) {
            $filters[] = ['label' => 'Admission', 'value' => $this->periodLabel($request->date_from ?? '0001-01-01', $request->date_to ?? now()->format('Y-m-d'))];
        }

        $report = $this->baseReport(
            'patients',
            'Patients Report',
            'Patient registry with financial balances.',
            $filters
        );

        $report['totals'] = [
            ['label' => 'Total Patients', 'value' => $patients->count(), 'class' => 'primary'],
            ['label' => 'On Ward', 'value' => $patients->where('patient_status', 'on_ward')->count(), 'class' => 'success'],
            ['label' => 'Transferred', 'value' => $patients->where('patient_status', 'transferred')->count(), 'class' => 'warning'],
            ['label' => 'Discharged', 'value' => $patients->where('patient_status', 'discharged')->count(), 'class' => 'info'],
        ];

        $rows = [];
        foreach ($patients as $patient) {
            $totalDays = $this->patientTotalDays($patient);
            $totalAmount = $totalDays * (float) $patient->amount_to_pay;
            $paidAmount = (float) $patient->payments->sum('amount_paid');
            $balance = max(0, $totalAmount - $paidAmount);

            $rows[] = [
                $patient->name,
                $patient->ward ?? 'N/A',
                ucfirst($patient->gender),
                $patient->date_of_admission->format('Y-m-d'),
                $patient->statusLabel,
                $this->money($patient->amount_to_pay),
                $this->money($paidAmount),
                $this->money($balance),
            ];
        }

        $report['sections'][] = [
            'title' => 'Patient Records',
            'headers' => ['Name', 'Ward', 'Gender', 'Admission', 'Status', 'Daily Rate', 'Total Paid', 'Balance'],
            'rows' => $rows,
            'empty' => $patients->isEmpty() ? 'No patients found for the selected filters.' : null,
        ];

        return $report;
    }

    private function patientTotalDays(Patient $patient): int
    {
        $start = $patient->date_of_admission;
        $end = $patient->patient_status === 'discharged' && $patient->date_of_discharge
            ? $patient->date_of_discharge
            : now();

        return max(0, (int) $start->diffInDays($end));
    }

    private function outstanding(Request $request): array
    {
        $query = Patient::with('payments');

        if ($request->patient_status) {
            $query->where('patient_status', $request->patient_status);
        }

        if ($request->min_balance !== null && $request->min_balance !== '') {
            // Sub-filter applied below after balances are computed.
        }

        $patients = $query->orderBy('name')->get();

        $rows = [];
        $totalDue = 0;
        $totalPaid = 0;
        $totalBalance = 0;
        $patientsWithBalance = 0;

        foreach ($patients as $patient) {
            $due    = $patient->total_due;
            $paid   = $patient->total_paid;
            $bal    = $patient->balance;
            $days   = $patient->days_admitted;
            $owed   = $patient->days_owed;
            $daysUnpaid = $days - $owed;

            if ($bal <= 0) {
                continue; // skip fully-paid patients
            }

            if ($request->min_balance !== null && $request->min_balance !== '' && $bal < (float) $request->min_balance) {
                continue;
            }

            $totalDue += $due;
            $totalPaid += $paid;
            $totalBalance += $bal;
            $patientsWithBalance++;

            $rows[] = [
                $patient->name,
                $patient->ward ?? 'N/A',
                $patient->statusLabel,
                $patient->date_of_admission->format('Y-m-d'),
                $days,
                $this->money($due),
                $this->money($paid),
                $this->money($bal),
                $owed,
            ];
        }

        // Sort by balance desc.
        usort($rows, function ($a, $b) {
            return (float)str_replace([','], '', $b[7]) <=> (float)str_replace([','], '', $a[7]);
        });

        $filters = [];
        if ($request->patient_status) {
            $filters[] = ['label' => 'Status', 'value' => ucfirst(str_replace('_', ' ', $request->patient_status))];
        }
        if ($request->min_balance !== null && $request->min_balance !== '') {
            $filters[] = ['label' => 'Min Balance', 'value' => $this->money((float) $request->min_balance)];
        }

        $report = $this->baseReport(
            'outstanding',
            'Outstanding Balances Report',
            'Patients with unpaid balances, sorted from highest to lowest.',
            $filters
        );

        $report['totals'] = [
            ['label' => 'Patients Owed',  'value' => $patientsWithBalance, 'class' => 'warning'],
            ['label' => 'Total Due',      'value' => $this->money($totalDue), 'class' => 'primary'],
            ['label' => 'Total Paid',     'value' => $this->money($totalPaid), 'class' => 'success'],
            ['label' => 'Total Balance',  'value' => $this->money($totalBalance), 'class' => 'danger'],
        ];

        $report['sections'][] = [
            'title' => 'Patients With Outstanding Balances',
            'headers' => ['Patient', 'Ward', 'Status', 'Admitted', 'Days', 'Total Due', 'Total Paid', 'Balance', 'Days Owed'],
            'rows' => $rows,
            'empty' => empty($rows) ? 'No patients with outstanding balances match the selected filters.' : null,
        ];

        return $report;
    }

    private function caregivers(Request $request): array
    {
        $query = Caregiver::withCount('patients');

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', (bool) $request->status);
        }

        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        $caregivers = $query->orderBy('name')->get();

        $filters = [];
        if ($request->status !== null && $request->status !== '') {
            $filters[] = ['label' => 'Status', 'value' => $request->status ? 'Active' : 'Inactive'];
        }
        if ($request->gender) {
            $filters[] = ['label' => 'Gender', 'value' => ucfirst($request->gender)];
        }

        $report = $this->baseReport(
            'caregivers',
            'Caregivers Report',
            'Caregiver registry and patient assignments.',
            $filters
        );

        $report['totals'] = [
            ['label' => 'Total Caregivers', 'value' => $caregivers->count(), 'class' => 'primary'],
            ['label' => 'Active', 'value' => $caregivers->where('status', true)->count(), 'class' => 'success'],
            ['label' => 'Inactive', 'value' => $caregivers->where('status', false)->count(), 'class' => 'warning'],
        ];

        $report['sections'][] = [
            'title' => 'Caregiver Records',
            'headers' => ['Name', 'Phone', 'Gender', 'Education', 'Entry Date', 'Status', 'Assigned Patients'],
            'rows' => $caregivers->map(fn ($caregiver) => [
                $caregiver->name,
                $caregiver->phone,
                ucfirst($caregiver->gender),
                $caregiver->level_of_education ?? 'N/A',
                $caregiver->date_of_entry?->format('Y-m-d') ?? 'N/A',
                $caregiver->status ? 'Active' : 'Inactive',
                $caregiver->patients_count,
            ])->all(),
            'empty' => $caregivers->isEmpty() ? 'No caregivers found for the selected filters.' : null,
        ];

        return $report;
    }

    private function attendance(Request $request): array
    {
        [$dateFrom, $dateTo] = $this->defaultPeriod($request);

        $query = Attendance::with(['caregiver', 'patient'])->whereBetween('date', [$dateFrom, $dateTo]);

        if ($request->caregiver_id) {
            $query->where('caregiver_id', $request->caregiver_id);
        }

        if ($request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $filters = [
            ['label' => 'Period', 'value' => $this->periodLabel($dateFrom, $dateTo)],
        ];
        if ($request->caregiver_id) {
            $filters[] = ['label' => 'Caregiver', 'value' => Caregiver::find($request->caregiver_id)?->name ?? 'N/A'];
        }
        if ($request->patient_id) {
            $filters[] = ['label' => 'Patient', 'value' => Patient::find($request->patient_id)?->name ?? 'N/A'];
        }

        $report = $this->baseReport(
            'attendance',
            'Attendance Report',
            'Daily attendance, checkup and complaint records.',
            $filters
        );

        $complaints = $attendances->filter(fn ($att) => filled($att->complaint_reported))->count();

        $report['totals'] = [
            ['label' => 'Total Records', 'value' => $attendances->count(), 'class' => 'primary'],
            ['label' => 'Complaints Reported', 'value' => $complaints, 'class' => 'warning'],
        ];

        $report['sections'][] = [
            'title' => 'Attendance Records',
            'headers' => ['Date', 'Caregiver', 'Patient', 'Ward', 'Days Under Care', 'Complaint'],
            'rows' => $attendances->map(fn ($attendance) => [
                $attendance->date->format('Y-m-d'),
                $attendance->caregiver->name ?? 'N/A',
                $attendance->patient->name ?? 'N/A',
                $attendance->ward ?? 'N/A',
                $attendance->days_under_care,
                $attendance->complaint_reported ? 'Yes' : 'No',
            ])->all(),
            'empty' => $attendances->isEmpty() ? 'No attendance records found for the selected filters.' : null,
        ];

        return $report;
    }

    /**
     * General Attendance (Ward Round) report.
     *
     * Shows every active patient currently on ward, grouped by ward, with
     * their assigned caregivers and visit status for the selected date.
     * Designed for the admin's daily ward-round workflow — they can quickly
     * see which patients still need to be visited today, and which patients
     * have reported complaints.
     */
    private function attendanceGeneral(Request $request): array
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $dateLabel = Carbon::parse($date)->format('D, d M Y');

        // Eager-load every active, on-ward patient with their active caregivers.
        $patients = Patient::with(['caregivers' => function ($q) {
                $q->orderBy('name');
            }])
            ->where('is_active', true)
            ->where('patient_status', 'on_ward')
            ->orderBy('ward')
            ->orderBy('name')
            ->get();

        if ($request->ward) {
            $patients = $patients->filter(
                fn ($p) => strcasecmp((string) $p->ward, (string) $request->ward) === 0
            )->values();
        }

        if ($request->caregiver_id) {
            $patients = $patients->filter(
                fn ($p) => $p->caregivers->pluck('id')->contains((int) $request->caregiver_id)
            )->values();
        }

        // Visits recorded on the selected date (keyed by patient_id).
        $todayVisits = Attendance::with('caregiver')
            ->whereDate('date', $date)
            ->get()
            ->keyBy('patient_id');

        // Per-patient ward-round rows (full listing + pending subset).
        $roundRows = [];
        $pendingRows = [];
        foreach ($patients as $pt) {
            $caregiversList = $pt->caregivers->pluck('name')->implode(', ') ?: '— Unassigned —';
            $daysUnderCare = $pt->days_admitted;
            $visit = $todayVisits->get($pt->id);

            $status    = $visit ? 'Visited' : 'Pending';
            $complaint = $visit && filled($visit->complaint_reported) ? 'Yes' : 'None';
            $followUp  = $visit && filled($visit->follow_up) ? 'Yes' : 'None';

            $row = [
                $pt->ward ?: '—',
                $pt->name,
                $caregiversList,
                $daysUnderCare,
                $status,
                $complaint,
                $followUp,
            ];

            $roundRows[] = $row;
            if (! $visit) {
                $pendingRows[] = $row;
            }
        }

        $filters = [
            ['label' => 'Round Date', 'value' => $dateLabel],
        ];
        if ($request->ward) {
            $filters[] = ['label' => 'Ward', 'value' => $request->ward];
        }
        if ($request->caregiver_id) {
            $filters[] = ['label' => 'Caregiver', 'value' => Caregiver::find($request->caregiver_id)?->name ?? 'N/A'];
        }

        $report = $this->baseReport(
            'attendance-general',
            'General Attendance — Ward Round',
            "Daily ward-round overview for {$dateLabel}. Lists every on-ward patient with their assigned caregivers and visit status.",
            $filters
        );

        $totalVisited = $patients->filter(fn ($pt) => $todayVisits->has($pt->id))->count();
        $totalComplaints = $todayVisits->filter(fn ($v) => filled($v->complaint_reported))->count();

        $report['totals'] = [
            ['label' => 'Patients on Ward', 'value' => $patients->count(), 'class' => 'primary'],
            ['label' => 'Visited Today',    'value' => $totalVisited,      'class' => 'success'],
            ['label' => 'Pending Visits',   'value' => max(0, $patients->count() - $totalVisited), 'class' => 'warning'],
            ['label' => 'Complaints Today', 'value' => $totalComplaints,    'class' => 'danger'],
        ];

        $report['sections'][] = [
            'title'   => 'Patients Not Yet Visited Today',
            'headers' => ['Ward', 'Patient', 'Caregiver(s)', 'Days Under Care', 'Status', 'Complaint', 'Follow Up'],
            'rows'    => $pendingRows,
            'empty'   => empty($pendingRows) ? 'All on-ward patients have been visited today.' : null,
        ];

        $report['sections'][] = [
            'title'   => 'Full Ward Round — ' . $dateLabel,
            'headers' => ['Ward', 'Patient', 'Caregiver(s)', 'Days Under Care', 'Status', 'Complaint', 'Follow Up'],
            'rows'    => $roundRows,
            'empty'   => empty($roundRows) ? 'No on-ward patients match the selected filters.' : null,
        ];

        return $report;
    }

    public function filterForm(string $type, Request $request): array
    {
        $patients = Patient::orderBy('name')->get(['id', 'name']);
        $caregivers = Caregiver::orderBy('name')->get(['id', 'name']);
        $categories = Expense::categories();

        $forms = [
            'financial' => [
                $this->field($request, 'date', 'date_from', 'From'),
                $this->field($request, 'date', 'date_to', 'To'),
            ],
            'payments' => [
                $this->selectField($request, 'patient_id', 'Patient', $patients->pluck('name', 'id')->all(), 'All Patients'),
                $this->field($request, 'date', 'date_from', 'From'),
                $this->field($request, 'date', 'date_to', 'To'),
                $this->selectField($request, 'payment_method', 'Method', ['cash' => 'Cash', 'bank' => 'Bank Transfer', 'mobile_money' => 'Mobile Money', 'other' => 'Other'], 'All Methods'),
            ],
            'expenses' => [
                $this->selectField($request, 'category', 'Category', $categories, 'All Categories'),
                $this->field($request, 'date', 'date_from', 'From'),
                $this->field($request, 'date', 'date_to', 'To'),
            ],
            'patients' => [
                $this->selectField($request, 'patient_status', 'Status', ['on_ward' => 'On Ward', 'transferred' => 'Transferred', 'discharged' => 'Discharged'], 'All Statuses'),
                $this->selectField($request, 'gender', 'Gender', ['male' => 'Male', 'female' => 'Female'], 'All Genders'),
                $this->field($request, 'text', 'ward', 'Ward'),
                $this->field($request, 'date', 'date_from', 'Admission From'),
                $this->field($request, 'date', 'date_to', 'Admission To'),
            ],
            'outstanding' => [
                $this->selectField($request, 'patient_status', 'Status', ['on_ward' => 'On Ward', 'transferred' => 'Transferred', 'discharged' => 'Discharged'], 'All Statuses'),
                $this->field($request, 'number', 'min_balance', 'Min Balance'),
            ],
            'caregivers' => [
                $this->selectField($request, 'status', 'Status', ['1' => 'Active', '0' => 'Inactive'], 'All Statuses'),
                $this->selectField($request, 'gender', 'Gender', ['male' => 'Male', 'female' => 'Female'], 'All Genders'),
            ],
            'attendance' => [
                $this->field($request, 'date', 'date_from', 'From'),
                $this->field($request, 'date', 'date_to', 'To'),
                $this->selectField($request, 'caregiver_id', 'Caregiver', $caregivers->pluck('name', 'id')->all(), 'All Caregivers'),
                $this->selectField($request, 'patient_id', 'Patient', $patients->pluck('name', 'id')->all(), 'All Patients'),
            ],
            'attendance-general' => [
                $this->field($request, 'date', 'date', 'Round Date'),
                $this->field($request, 'text', 'ward', 'Ward'),
                $this->selectField($request, 'caregiver_id', 'Caregiver', $caregivers->pluck('name', 'id')->all(), 'All Caregivers'),
            ],
        ];

        return $forms[$type] ?? [];
    }

    private function field(Request $request, string $type, string $name, string $label): array
    {
        return [
            'name' => $name,
            'type' => $type,
            'label' => $label,
            'value' => $request->input($name),
        ];
    }

    private function selectField(Request $request, string $name, string $label, array $options, string $placeholder): array
    {
        return [
            'name' => $name,
            'type' => 'select',
            'label' => $label,
            'value' => $request->input($name),
            'placeholder' => $placeholder,
            'options' => $options,
        ];
    }
}
