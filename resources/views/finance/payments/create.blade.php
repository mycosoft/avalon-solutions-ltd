@extends('adminlte::page')

@section('title', 'Record Payment')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Record Payment</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Payments</a></li>
                    <li class="breadcrumb-item active">Record</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Payment Form</h3>
                    </div>
                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id">Patient *</label>
                                        <select class="form-control @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                            <option value="">Select Patient</option>
                                            @foreach($patients as $pt)
                                                <option value="{{ $pt->id }}" {{ old('patient_id', $patient?->id) == $pt->id ? 'selected' : '' }}>{{ $pt->name }} - {{ number_format($pt->amount_to_pay, 2) }}/day</option>
                                            @endforeach
                                        </select>
                                        @error('patient_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payee_name">Payee Name (Client/Relative) *</label>
                                        <input type="text" class="form-control @error('payee_name') is-invalid @enderror" id="payee_name" name="payee_name" value="{{ old('payee_name') }}" required placeholder="Who is making the payment">
                                        @error('payee_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_date">Payment Date *</label>
                                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                        @error('payment_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method *</label>
                                        <select class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                            <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('payment_method')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="days_paid">Days Paid *</label>
                                        <input type="number" class="form-control @error('days_paid') is-invalid @enderror" id="days_paid" name="days_paid" value="{{ old('days_paid', 1) }}" min="1" required>
                                        @error('days_paid')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="amount_paid">Amount Paid *</label>
                                        <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" value="{{ old('amount_paid') }}" required placeholder="Enter amount">
                                        @error('amount_paid')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Balance</label>
                                        <input type="text" class="form-control" id="balance_display" value="0.00" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Record Payment</button>
                            <a href="{{ route('payments.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            @if($patient)
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Patient Payment Summary</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th>Patient</th>
                                <td>{{ $patient->name }}</td>
                            </tr>
                            <tr>
                                <th>Daily Rate</th>
                                <td>{{ number_format($patient->amount_to_pay, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Admission Date</th>
                                <td>{{ $patient->date_of_admission->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Total Days</th>
                                <td>{{ $totalDays }}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>{{ number_format($totalAmount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Already Paid</th>
                                <td>{{ number_format($paidAmount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Current Balance</th>
                                <td class="text-danger font-weight-bold">{{ number_format($balance, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

@section('js')
<script>
    $('#patient_id').change(function() {
        var patientId = $(this).val();
        if (patientId) {
            var dailyRate = $('option:selected', this).text().match(/[\d,]+\.\d{2}/);
            $.get('/payments/patient-balance/' + patientId, function(data) {
                $('#balance_display').val(data.balance.toFixed(2));
                if (data.daily_rate) {
                    var suggestedAmount = data.daily_rate;
                    $('#amount_paid').attr('placeholder', 'Min: ' + suggestedAmount.toFixed(2));
                }
            });
        }
    });
</script>
@stop
