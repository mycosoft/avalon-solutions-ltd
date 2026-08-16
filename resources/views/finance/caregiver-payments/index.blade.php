@extends('adminlte::page')

@section('title', 'Caregiver Payments')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Caregiver Payments</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Caregiver Payments</li>
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
                        <h3 class="card-title">Caregiver Payment Records</h3>
                        <div class="card-tools">
                            <a href="{{ route('caregiver-payments.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Pay Caregiver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('caregiver-payments.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="caregiver_id" class="form-control">
                                        <option value="">All Caregivers</option>
                                        @foreach($caregivers as $cg)
                                            <option value="{{ $cg->id }}" {{ request('caregiver_id') == $cg->id ? 'selected' : '' }}>{{ $cg->name }}</option>
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
                                    <th>Caregiver</th>
                                    <th>Period</th>
                                    <th>Monthly Rate</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th width="140">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $pmt)
                                    <tr>
                                        <td>{{ $pmt->id }}</td>
                                        <td>{{ $pmt->payment_date->format('Y-m-d') }}</td>
                                        <td>{{ $pmt->caregiver->name ?? 'N/A' }}</td>
                                        <td>
                                            <small>
                                                {{ $pmt->period_start->format('M d') }} -
                                                {{ $pmt->period_end->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>{{ number_format($pmt->monthly_rate ?? 0, 2) }}</td>
                                        <td><strong>{{ number_format($pmt->amount_paid, 2) }}</strong></td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $pmt->payment_method)) }}</td>
                                        <td>
                                            <a href="{{ route('caregiver-payments.show', $pmt->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('caregiver-payments.receipt', $pmt->id) }}" class="btn btn-default btn-sm" target="_blank" title="Print Receipt">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No caregiver payments found.</td>
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
