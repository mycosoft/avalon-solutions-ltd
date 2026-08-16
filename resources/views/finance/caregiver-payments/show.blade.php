@extends('adminlte::page')

@section('title', 'Caregiver Payment Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Caregiver Payment Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('caregiver-payments.index') }}">Caregiver Payments</a></li>
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
                            <tr><th width="150">Receipt #</th><td><code>{{ $payment->receipt_number }}</code></td></tr>
                            <tr><th>Caregiver</th><td>{{ $payment->caregiver->name ?? 'N/A' }}</td></tr>
                            <tr><th>Recorded By</th><td>{{ $payment->payee_name }}</td></tr>
                            <tr><th>Payment Date</th><td>{{ $payment->payment_date->format('Y-m-d') }}</td></tr>
                            <tr><th>Period</th>
                                <td>
                                    {{ $payment->period_start->format('Y-m-d') }}
                                    &nbsp;to&nbsp;
                                    {{ $payment->period_end->format('Y-m-d') }}
                                </td>
                            </tr>
                            <tr><th>Payment Method</th><td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Financial Details</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr><th width="150">Monthly Rate</th><td>{{ number_format($payment->monthly_rate ?? 0, 2) }}</td></tr>
                            <tr><th>Amount Paid</th><td class="text-info font-weight-bold" style="font-size: 1.25rem;">{{ number_format($payment->amount_paid, 2) }}</td></tr>
                        </table>
                        <a href="{{ route('caregiver-payments.receipt', $payment->id) }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-print mr-1"></i> Print Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->notes)
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header"><h3 class="card-title">Notes</h3></div>
                    <div class="card-body"><p>{{ $payment->notes }}</p></div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('caregiver-payments.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i> Back</a>
            </div>
        </div>
    </div>
@stop
