@extends('adminlte::page')

@section('title', 'Avalon Solutions - Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Welcome Back, {{ auth()->user()->name }}!</h1>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
    .content-wrapper {
        background-color: #e9ecef !important;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">

        @if(auth()->user()->role_type === 'superadmin' || auth()->user()->role_type === 'admin')
        {{-- Row 1: 4 Financial Stats --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-gradient-info">
                    <div class="inner" style="padding: 10px;">
                        <h3 style="font-size: 24px; margin: 0;">{{ number_format($stats['payments']['today'], 0) }}</h3>
                        <p style="margin-bottom: 0;">Collections Today</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill" style="font-size: 45px; top: 15px;"></i></div>
                    <a href="{{ route('payments.index') }}" class="small-box-footer" style="padding: 3px 0; z-index: 10;">View Payments <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-gradient-success">
                    <div class="inner" style="padding: 10px;">
                        <h3 style="font-size: 24px; margin: 0;">{{ number_format($stats['payments']['month'], 0) }}</h3>
                        <p style="margin-bottom: 0;">Collections This Month</p>
                    </div>
                    <div class="icon"><i class="fas fa-dollar-sign" style="font-size: 45px; top: 15px;"></i></div>
                    <a href="{{ route('payments.index') }}" class="small-box-footer" style="padding: 3px 0; z-index: 10;">View Payments <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-gradient-danger">
                    <div class="inner" style="padding: 10px;">
                        <h3 style="font-size: 24px; margin: 0;">{{ number_format($stats['expenses']['today'], 0) }}</h3>
                        <p style="margin-bottom: 0;">Expenses Today</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-invoice-dollar" style="font-size: 45px; top: 15px;"></i></div>
                    <a href="{{ route('expenses.index') }}" class="small-box-footer" style="padding: 3px 0; z-index: 10;">View Expenses <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-gradient-dark">
                    <div class="inner" style="padding: 10px;">
                        <h3 style="font-size: 24px; margin: 0;">{{ number_format($stats['expenses']['month'], 0) }}</h3>
                        <p style="margin-bottom: 0;">Expenses This Month</p>
                    </div>
                    <div class="icon"><i class="fas fa-receipt" style="font-size: 45px; top: 15px;"></i></div>
                    <a href="{{ route('expenses.index') }}" class="small-box-footer" style="padding: 3px 0; z-index: 10;">View Expenses <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Row 2: 4 Key Stats (Info Box Style) --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-primary"><i class="fas fa-user-nurse"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Caregivers</span>
                        <span class="info-box-number">{{ $stats['total_caregivers'] }}</span>
                        <a href="{{ route('caregivers.index') }}" class="text-muted small">View All <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-success"><i class="fas fa-procedures"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Patients</span>
                        <span class="info-box-number">{{ $stats['total_patients'] }}</span>
                        <a href="{{ route('patients.index') }}" class="text-muted small">View All <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-warning text-white"><i class="fas fa-bed"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Patients On Ward</span>
                        <span class="info-box-number">{{ $stats['patients_on_ward'] }}</span>
                        <a href="{{ route('patients.index') }}" class="text-muted small">View All <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Today's Attendance</span>
                        <span class="info-box-number">{{ $stats['today_attendance'] }}</span>
                        <a href="{{ route('attendance.index') }}" class="text-muted small">View All <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Charts --}}
        <div class="row">
            <div class="col-lg-8 col-12">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>6-Month Payments & Expenses Trend</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentsExpensesChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>User Role Distribution</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body" style="position:relative; min-height:220px;">
                        <canvas id="roleDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: Summary Cards --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card card-success shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-money-bill mr-2"></i>Payments Summary</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-sun mr-1 text-warning"></i> Today</td>
                                    <td class="text-right font-weight-bold text-success">{{ number_format($stats['payments']['today'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-week mr-1 text-info"></i> This Week</td>
                                    <td class="text-right">{{ number_format($stats['payments']['week'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-alt mr-1 text-primary"></i> This Month</td>
                                    <td class="text-right">{{ number_format($stats['payments']['month'], 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card card-danger shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-2"></i>Expenses Summary</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-sun mr-1 text-warning"></i> Today</td>
                                    <td class="text-right font-weight-bold text-danger">{{ number_format($stats['expenses']['today'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-week mr-1 text-info"></i> This Week</td>
                                    <td class="text-right">{{ number_format($stats['expenses']['week'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-alt mr-1 text-primary"></i> This Month</td>
                                    <td class="text-right">{{ number_format($stats['expenses']['month'], 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card card-primary shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-procedures mr-2"></i>Patient Stats</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-bed mr-1 text-warning"></i> On Ward</td>
                                    <td class="text-right font-weight-bold text-primary">{{ $stats['patients_on_ward'] }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-user-check mr-1 text-success"></i> Discharged</td>
                                    <td class="text-right">{{ $stats['patients_discharged'] }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-users mr-1 text-info"></i> Total</td>
                                    <td class="text-right">{{ $stats['total_patients'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card card-dark shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users mr-2"></i>System Users</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-user-tie mr-1 text-warning"></i> Total Users</td>
                                    <td class="text-right font-weight-bold">{{ $stats['total_users'] }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-user-nurse mr-1 text-info"></i> Caregivers</td>
                                    <td class="text-right">{{ $stats['total_caregivers'] }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-check mr-1 text-success"></i> Today's Attendance</td>
                                    <td class="text-right">{{ $stats['today_attendance'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 5: Recent Patients & Attendance --}}
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h3 class="card-title"><i class="fas fa-procedures text-success mr-2"></i>Recent Patients</h3>
                        <div class="card-tools">
                            <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-success">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Ward</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_patients'] as $patient)
                                <tr>
                                    <td>{{ $patient->name }}</td>
                                    <td>{{ $patient->ward ?? 'N/A' }}</td>
                                    <td><span class="badge badge-{{ $patient->patient_status === 'on_ward' ? 'primary' : ($patient->patient_status === 'transferred' ? 'warning' : 'success') }}">{{ $patient->status_label }}</span></td>
                                    <td>{{ $patient->date_of_admission->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No patients found.</td></tr>
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
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-gradient-success">
                    <div class="inner" style="padding: 10px;">
                        <h3 style="font-size: 24px; margin: 0;">{{ number_format($stats['payments']['today'], 0) }}</h3>
                        <p style="margin-bottom: 0;">Collections Today</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill" style="font-size: 45px; top: 15px;"></i></div>
                    <a href="{{ route('payments.index') }}" class="small-box-footer" style="padding: 3px 0; z-index: 10;">View Payments <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-gradient-info">
                    <div class="inner" style="padding: 10px;">
                        <h3 style="font-size: 24px; margin: 0;">{{ number_format($stats['payments']['month'], 0) }}</h3>
                        <p style="margin-bottom: 0;">Collections This Month</p>
                    </div>
                    <div class="icon"><i class="fas fa-dollar-sign" style="font-size: 45px; top: 15px;"></i></div>
                    <a href="{{ route('payments.index') }}" class="small-box-footer" style="padding: 3px 0; z-index: 10;">View Payments <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-gradient-danger">
                    <div class="inner" style="padding: 10px;">
                        <h3 style="font-size: 24px; margin: 0;">{{ number_format($stats['expenses']['today'], 0) }}</h3>
                        <p style="margin-bottom: 0;">Expenses Today</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-invoice-dollar" style="font-size: 45px; top: 15px;"></i></div>
                    <a href="{{ route('expenses.index') }}" class="small-box-footer" style="padding: 3px 0; z-index: 10;">View Expenses <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-gradient-dark">
                    <div class="inner" style="padding: 10px;">
                        <h3 style="font-size: 24px; margin: 0;">{{ number_format($stats['expenses']['month'], 0) }}</h3>
                        <p style="margin-bottom: 0;">Expenses This Month</p>
                    </div>
                    <div class="icon"><i class="fas fa-receipt" style="font-size: 45px; top: 15px;"></i></div>
                    <a href="{{ route('expenses.index') }}" class="small-box-footer" style="padding: 3px 0; z-index: 10;">View Expenses <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row">
            <div class="col-lg-8 col-12">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>6-Month Payments & Expenses Trend</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentsExpensesChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>User Role Distribution</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body" style="position:relative; min-height:220px;">
                        <canvas id="roleDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="card card-success shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-money-bill mr-2"></i>Payments Summary</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-sun mr-1 text-warning"></i> Today</td>
                                    <td class="text-right font-weight-bold text-success">{{ number_format($stats['payments']['today'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-week mr-1 text-info"></i> This Week</td>
                                    <td class="text-right">{{ number_format($stats['payments']['week'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-alt mr-1 text-primary"></i> This Month</td>
                                    <td class="text-right">{{ number_format($stats['payments']['month'], 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="card card-danger shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-2"></i>Expenses Summary</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-sun mr-1 text-warning"></i> Today</td>
                                    <td class="text-right font-weight-bold text-danger">{{ number_format($stats['expenses']['today'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-week mr-1 text-info"></i> This Week</td>
                                    <td class="text-right">{{ number_format($stats['expenses']['week'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-alt mr-1 text-primary"></i> This Month</td>
                                    <td class="text-right">{{ number_format($stats['expenses']['month'], 0) }}</td>
                                </tr>
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
                maintainAspectRatio: true,
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

    // Doughnut Chart: Role Distribution
    const roleData  = @json($stats['role_distribution']);
    const roleLabels = Object.keys(roleData).map(r => r.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
    const roleValues = Object.values(roleData);
    const pieColors  = ['#007bff','#28a745','#dc3545','#ffc107','#17a2b8','#6f42c1','#fd7e14'];

    const pieCtx = document.getElementById('roleDistributionChart').getContext('2d');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: roleValues,
                    backgroundColor: pieColors.slice(0, roleValues.length),
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
                                return ' ' + ctx.label + ': ' + ctx.raw + ' users';
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
