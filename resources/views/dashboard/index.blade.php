@extends('adminlte::page')

@section('title', 'Avalon Solutions - Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 welcome-heading">Welcome Back, {{ auth()->user()->name }}!</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <span class="text-muted">
                        <i class="far fa-calendar-alt mr-1"></i>
                        {{ now()->format('l, F d, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
    .content-wrapper {
        background-color: #e9ecef !important;
    }

    /* =========================================================
       Info Boxes — AdminLTE Format (rounded + hover polish)
       ========================================================= */
    .info-box {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        transition: transform .2s ease, box-shadow .2s ease;
        min-height: 96px;
    }
    .info-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.20);
    }
    .info-box .info-box-icon {
        font-size: 28px;
    }
    .info-box .info-box-text {
        font-size: 12px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        opacity: 0.92;
    }
    .info-box .info-box-number {
        font-size: 26px;
        font-weight: 700;
        letter-spacing: -0.4px;
    }
    .info-box .progress {
        background-color: rgba(0, 0, 0, 0.18);
        height: 3px;
        margin: 6px 0 2px;
        border-radius: 999px;
        overflow: hidden;
        border: 0;
    }
    .info-box .progress .progress-bar {
        background-color: rgba(255, 255, 255, 0.95);
        box-shadow: 0 0 4px rgba(255, 255, 255, 0.45);
    }
    .info-box .progress-description {
        color: rgba(255, 255, 255, 0.92);
        font-size: 12px;
        display: block;
        margin-top: 2px;
    }

    /* Dashboard greeting - slightly smaller than the AdminLTE default */
    .content-header .welcome-heading {
        font-size: 1.15rem !important;
        font-weight: 600;
        letter-spacing: -0.2px;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">

        @if(auth()->user()->role_type === 'superadmin' || auth()->user()->role_type === 'admin')
        {{-- Row 1: Summary Stat Cards (AdminLTE info-box) --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Payments This Month</span>
                        <span class="info-box-number">{{ number_format($stats['payments']['month'], 0) }}</span>
                        @php
                            $paymentsMonth = max(1, (float) $stats['payments']['month']);
                            $paymentsToday = (float) $stats['payments']['today'];
                            $paymentsPct   = min(100, (int) round(($paymentsToday / $paymentsMonth) * 100));
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $paymentsPct }}%"></div>
                        </div>
                        <span class="progress-description">
                            <strong>{{ number_format($stats['payments']['today'], 0) }}</strong> received today
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-procedures"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Patients</span>
                        <span class="info-box-number">{{ $stats['total_patients'] }}</span>
                        @php
                            $patientsTotal  = max(1, (int) $stats['total_patients']);
                            $patientsOnWard = (int) $stats['patients_on_ward'];
                            $patientsPct    = min(100, (int) round(($patientsOnWard / $patientsTotal) * 100));
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $patientsPct }}%"></div>
                        </div>
                        <span class="progress-description">
                            <strong>{{ $patientsOnWard }}</strong> currently on ward
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-user-nurse"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Caregivers</span>
                        <span class="info-box-number">{{ $stats['total_caregivers'] }}</span>
                        @php
                            $caregiversTotal  = max(1, (int) $stats['total_caregivers']);
                            $caregiversActive = (int) $stats['active_caregivers'];
                            $caregiversPct    = min(100, (int) round(($caregiversActive / $caregiversTotal) * 100));
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $caregiversPct }}%"></div>
                        </div>
                        <span class="progress-description">
                            <strong>{{ $caregiversActive }}</strong> active
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Expenses</span>
                        <span class="info-box-number">{{ number_format($stats['expenses']['month'], 0) }}</span>
                        @php
                            $expensesMonth = max(1, (float) $stats['expenses']['month']);
                            $expensesToday = (float) $stats['expenses']['today'];
                            $expensesPct   = min(100, (int) round(($expensesToday / $expensesMonth) * 100));
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $expensesPct }}%"></div>
                        </div>
                        <span class="progress-description">
                            <strong>{{ number_format($stats['expenses']['today'], 0) }}</strong> spent today
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Charts --}}
        <div class="row">
            <div class="col-lg-8 col-12">
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Payments Vs Expenses</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 320px;">
                            <canvas id="paymentsExpensesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Patient Status Distribution</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 320px;">
                            <canvas id="roleDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 5: Recent Payments & Attendance --}}
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info">
                        <h3 class="card-title text-white"><i class="fas fa-money-bill text-white mr-2"></i>Recent Payments</h3>
                        <div class="card-tools">
                            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-light">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Payee</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_payments'] as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>{{ $payment->patient->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->payee_name }}</td>
                                    <td>{{ number_format($payment->amount_paid, 0) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No payments found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h3 class="card-title"><i class="fas fa-calendar-check text-warning mr-2"></i>Recent Attendance</h3>
                        <div class="card-tools">
                            <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Caregiver</th>
                                    <th>Patient</th>
                                    <th>Complaint</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_attendances'] as $att)
                                <tr>
                                    <td>{{ $att->date->format('M d, Y') }}</td>
                                    <td>{{ $att->caregiver->name ?? 'N/A' }}</td>
                                    <td>{{ $att->patient->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($att->complaint_reported)
                                            <span class="badge badge-danger">Yes</span>
                                        @else
                                            <span class="badge badge-success">None</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No attendance records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->role_type === 'accountant')
        {{-- Accountant Dashboard --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Payments This Month</span>
                        <span class="info-box-number">{{ number_format($stats['payments']['month'], 0) }}</span>
                        @php
                            $paymentsMonth = max(1, (float) $stats['payments']['month']);
                            $paymentsToday = (float) $stats['payments']['today'];
                            $paymentsPct   = min(100, (int) round(($paymentsToday / $paymentsMonth) * 100));
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $paymentsPct }}%"></div>
                        </div>
                        <span class="progress-description">
                            <strong>{{ number_format($stats['payments']['today'], 0) }}</strong> received today
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-procedures"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Patients</span>
                        <span class="info-box-number">{{ $stats['total_patients'] }}</span>
                        @php
                            $patientsTotal  = max(1, (int) $stats['total_patients']);
                            $patientsOnWard = (int) $stats['patients_on_ward'];
                            $patientsPct    = min(100, (int) round(($patientsOnWard / $patientsTotal) * 100));
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $patientsPct }}%"></div>
                        </div>
                        <span class="progress-description">
                            <strong>{{ $patientsOnWard }}</strong> currently on ward
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-user-nurse"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Caregivers</span>
                        <span class="info-box-number">{{ $stats['total_caregivers'] }}</span>
                        @php
                            $caregiversTotal  = max(1, (int) $stats['total_caregivers']);
                            $caregiversActive = (int) $stats['active_caregivers'];
                            $caregiversPct    = min(100, (int) round(($caregiversActive / $caregiversTotal) * 100));
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $caregiversPct }}%"></div>
                        </div>
                        <span class="progress-description">
                            <strong>{{ $caregiversActive }}</strong> active
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Expenses</span>
                        <span class="info-box-number">{{ number_format($stats['expenses']['month'], 0) }}</span>
                        @php
                            $expensesMonth = max(1, (float) $stats['expenses']['month']);
                            $expensesToday = (float) $stats['expenses']['today'];
                            $expensesPct   = min(100, (int) round(($expensesToday / $expensesMonth) * 100));
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $expensesPct }}%"></div>
                        </div>
                        <span class="progress-description">
                            <strong>{{ number_format($stats['expenses']['today'], 0) }}</strong> spent today
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row">
            <div class="col-lg-8 col-12">
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Payments Vs Expenses</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 320px;">
                            <canvas id="paymentsExpensesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Patient Status Distribution</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 320px;">
                            <canvas id="roleDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row: Recent Payments & Attendance --}}
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info">
                        <h3 class="card-title text-white"><i class="fas fa-money-bill text-white mr-2"></i>Recent Payments</h3>
                        <div class="card-tools">
                            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-light">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Payee</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_payments'] as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>{{ $payment->patient->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->payee_name }}</td>
                                    <td>{{ number_format($payment->amount_paid, 0) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No payments found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h3 class="card-title"><i class="fas fa-calendar-check text-warning mr-2"></i>Recent Attendance</h3>
                        <div class="card-tools">
                            <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Caregiver</th>
                                    <th>Patient</th>
                                    <th>Complaint</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_attendances'] as $att)
                                <tr>
                                    <td>{{ $att->date->format('M d, Y') }}</td>
                                    <td>{{ $att->caregiver->name ?? 'N/A' }}</td>
                                    <td>{{ $att->patient->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($att->complaint_reported)
                                            <span class="badge badge-danger">Yes</span>
                                        @else
                                            <span class="badge badge-success">None</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No attendance records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @endif

    </div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Line Chart: Payments & Expenses
    const chartLabels   = @json($stats['chart']['labels']);
    const chartPayments    = @json($stats['chart']['payments']);
    const chartExpenses = @json($stats['chart']['expenses']);

    const lineCtx = document.getElementById('paymentsExpensesChart').getContext('2d');
    if (lineCtx) {
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Payments',
                        data: chartPayments,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.12)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#28a745',
                        pointRadius: 5,
                        borderWidth: 2,
                    },
                    {
                        label: 'Expenses',
                        data: chartExpenses,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220,53,69,0.10)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#dc3545',
                        pointRadius: 5,
                        borderWidth: 2,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Doughnut Chart: Patient Status Distribution
    const statusData    = @json($stats['patient_status_distribution']);
    const statusLabelsMap = { on_ward: 'On Ward', transferred: 'Transferred', discharged: 'Discharged' };
    const statusLabels = Object.keys(statusData).map(k => statusLabelsMap[k] || k.replace(/_/g, ' '));
    const statusValues = Object.values(statusData);
    const pieColors  = ['#007bff','#ffc107','#28a745','#dc3545','#17a2b8','#6f42c1','#fd7e14'];

    const pieCtx = document.getElementById('roleDistributionChart').getContext('2d');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: pieColors.slice(0, statusValues.length),
                    borderWidth: 2,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 10, boxWidth: 14 } },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.label + ': ' + ctx.raw + ' patients';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
