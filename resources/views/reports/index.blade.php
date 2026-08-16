@extends('adminlte::page')

@section('title', 'Reports')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Reports</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>All Available Reports</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="reports-list list-group list-group-flush">
                            @foreach($types as $key => $label)
                                @php
                                    $reportMeta = [
                                        'financial'  => ['icon' => 'fa-chart-pie',          'desc' => 'Income & expense overview with method and category breakdowns.',           'color' => 'info'],
                                        'payments'   => ['icon' => 'fa-money-bill-wave',     'desc' => 'Detailed payment records with patient, method and balance details.',     'color' => 'success'],
                                        'expenses'   => ['icon' => 'fa-file-invoice-dollar', 'desc' => 'Detailed expense records grouped by category for the selected period.',   'color' => 'danger'],
                                        'patients'    => ['icon' => 'fa-procedures',           'desc' => 'Patient registry with daily rate, total paid and outstanding balance.',  'color' => 'primary'],
                                        'outstanding' => ['icon' => 'fa-exclamation-circle',   'desc' => 'Patients with unpaid balances, sorted from highest to lowest.',         'color' => 'danger'],
                                        'caregivers'  => ['icon' => 'fa-user-nurse',           'desc' => 'Caregiver registry with assigned patients and monthly rate.',           'color' => 'warning'],
                                        'attendance'  => ['icon' => 'fa-calendar-check',       'desc' => 'Daily attendance, checkup and complaint records for caregivers.',     'color' => 'secondary'],
                                    ];
                                    $meta = $reportMeta[$key] ?? ['icon' => 'fa-file-alt', 'desc' => '', 'color' => 'info'];
                                @endphp
                                <li class="list-group-item reports-list-item">
                                    <div class="reports-icon bg-{{ $meta['color'] }}">
                                        <i class="fas {{ $meta['icon'] }}"></i>
                                    </div>
                                    <div class="reports-text">
                                        <div class="reports-title">{{ $label }}</div>
                                        <div class="reports-desc">{{ $meta['desc'] }}</div>
                                    </div>
                                    <a href="{{ route('reports.show', $key) }}" class="btn btn-info btn-sm reports-action">
                                        Open <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    /* =========================================================
       Reports List — Clean Simple List Style
       ========================================================= */
    .reports-list-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        border-left: 0;
        border-right: 0;
        transition: background-color .15s ease;
    }
    .reports-list-item:first-child {
        border-top: 0;
    }
    .reports-list-item:hover {
        background-color: #f8fafc;
    }
    .reports-list-item + .reports-list-item {
        border-top: 1px solid #f1f5f9;
    }

    .reports-icon {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.10);
    }

    .reports-text {
        flex: 1;
        min-width: 0;
    }
    .reports-title {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .reports-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.45;
    }

    .reports-action {
        flex-shrink: 0;
        font-weight: 600;
    }

    @media (max-width: 575.98px) {
        .reports-list-item {
            flex-wrap: wrap;
            gap: 12px;
            padding: 14px 16px;
        }
        .reports-action {
            width: 100%;
        }
    }
</style>
@stop