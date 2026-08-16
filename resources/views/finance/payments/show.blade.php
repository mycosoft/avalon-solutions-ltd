@extends('adminlte::page')

@section('title', 'Payment Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Payment Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Payments</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Payment Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="150">Receipt #</th>
                                <td><code>{{ $payment->receipt_number }}</code></td>
                            </tr>
                            <tr>
                                <th>Patient</th>
                                <td>{{ $payment->patient->name }}</td>
                            </tr>
                            <tr>
                                <th>Payee Name</th>
                                <td>{{ $payment->payee_name }}</td>
                            </tr>
                            <tr>
                                <th>Payment Date</th>
                                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method</th>
                                <td>{{ ucfirst($payment->payment_method) }}</td>
                            </tr>
                            <tr>
                                <th>Payment Type</th>
                                <td>
                                    @if($payment->payment_type === 'partial')
                                        <span class="badge badge-warning">Partial</span>
                                    @else
                                        <span class="badge badge-success">Full</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Financial Details</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="150">Daily Rate</th>
                                <td>{{ number_format($payment->daily_rate, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Days Paid</th>
                                <td>{{ $payment->days_paid }}</td>
                            </tr>
                            <tr>
                                <th>Period Start</th>
                                <td>{{ $payment->period_start->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Period End</th>
                                <td>{{ $payment->period_end->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Amount Paid</th>
                                <td class="text-success font-weight-bold">{{ number_format($payment->amount_paid, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Remaining Balance</th>
                                <td class="text-danger font-weight-bold">
                                    @if((float) $payment->running_balance > 0)
                                        {{ number_format($payment->running_balance, 2) }}
                                    @else
                                        <span class="badge badge-success">Cleared</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->notes)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Notes</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $payment->notes }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Audit Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="150">Recorded By</th>
                                <td>{{ $payment->recorded_by ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $payment->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('payments.index') }}" class="btn btn-default">Back to Payments</a>
                <a href="{{ route('payments.receipt', $payment->id) }}" class="btn btn-info ml-2" target="_blank">
                    <i class="fas fa-print mr-1"></i> Print POS Receipt
                </a>
            </div>
        </div>
    </div>
@stop
