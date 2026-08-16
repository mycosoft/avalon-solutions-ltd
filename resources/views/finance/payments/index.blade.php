@extends('adminlte::page')

@section('title', 'Patient Payments')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Patient Payments</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Patient Payments</li>
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
                        <h3 class="card-title">Patient Payment Records</h3>
                        <div class="card-tools">
                            <a href="{{ route('caregiver-payments.index') }}" class="btn btn-sm mr-2 btn-avalon-white">
                                <i class="fas fa-user-nurse"></i> Caregiver Payments
                            </a>
                            <a href="{{ route('payments.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Record Payment
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('payments.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="patient_id" class="form-control">
                                        <option value="">All Patients</option>
                                        @foreach($patients as $pt)
                                            <option value="{{ $pt->id }}" {{ request('patient_id') == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-info">Filter</button>
                                </div>
                            </div>
                        </form>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Payee</th>
                                    <th>Amount</th>
                                    <th>Days</th>
                                    <th>Method</th>
                                    <th>Type</th>
                                    <th>Balance</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $pmt)
                                    <tr>
                                        <td>{{ $pmt->id }}</td>
                                        <td>{{ $pmt->payment_date->format('Y-m-d') }}</td>
                                        <td>
                                            @if($pmt->patient)
                                                <a href="{{ route('patients.show', $pmt->patient->id) }}">{{ $pmt->patient->name }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $pmt->payee_name }}</td>
                                        <td>{{ number_format($pmt->amount_paid, 0) }}</td>
                                        <td>{{ $pmt->days_paid }}</td>
                                        <td>{{ ucfirst($pmt->payment_method) }}</td>
                                        <td>
                                            @if($pmt->payment_type === 'partial')
                                                <span class="badge badge-warning">Partial</span>
                                            @else
                                                <span class="badge badge-success">Full</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($pmt->balance, 0) }}</td>
                                        <td>
                                            <a href="{{ route('payments.show', $pmt->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('payments.receipt', $pmt->id) }}" class="btn btn-default btn-sm" target="_blank" title="Print Receipt">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No payments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
