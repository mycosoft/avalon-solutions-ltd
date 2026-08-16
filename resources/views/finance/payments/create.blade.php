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
                                        <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" value="{{ old('amount_paid') }}" required>
                                        <small class="text-muted" id="rate_hint">Select a patient to auto-fill the amount.</small>
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
                        <h3 class="card-title">Patient Balance Summary</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <tr>
                                <th>Patient</th>
                                <td>{{ $patient->name }}</td>
                            </tr>
                            <tr>
                                <th>Daily Rate</th>
                                <td>{{ \App\Models\Setting::get('currency_symbol', 'UGX') }} {{ number_format($patient->amount_to_pay, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Admission Date</th>
                                <td>{{ $patient->date_of_admission->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Days Admitted</th>
                                <td><strong>{{ $totalDays }}</strong> {{ $patient->patient_status === 'discharged' ? '(discharged)' : 'days' }}</td>
                            </tr>
                            <tr class="bg-light">
                                <th>Total Due</th>
                                <td class="font-weight-bold">{{ \App\Models\Setting::get('currency_symbol', 'UGX') }} {{ number_format($totalDue, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Total Paid</th>
                                <td class="text-success">{{ \App\Models\Setting::get('currency_symbol', 'UGX') }} {{ number_format($paidAmount, 2) }}</td>
                            </tr>
                            <tr class="bg-warning">
                                <th>Balance Due</th>
                                <td class="font-weight-bold text-danger">{{ \App\Models\Setting::get('currency_symbol', 'UGX') }} {{ number_format($balance, 2) }}</td>
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
    var lastBalance = 0;
    var lastRate    = 0;

    function recalcAmount() {
        var days = parseInt($('#days_paid').val()) || 1;
        var suggested = (lastRate * days).toFixed(2);
        if (!$('#amount_paid').data('touched')) {
            $('#amount_paid').val(suggested);
        }
    }

    $('#patient_id').change(function() {
        var patientId = $(this).val();
        if (!patientId) {
            $('#balance_display').val('0.00');
            lastRate = 0;
            $('#amount_paid').data('touched', false).val('');
            $('#rate_hint').text('Select a patient to auto-fill the amount.');
            return;
        }

        $.get('/payments/patient-balance/' + patientId, function(data) {
            $('#balance_display').val(parseFloat(data.balance).toFixed(2));
            lastBalance = parseFloat(data.balance);
            lastRate    = parseFloat(data.daily_rate);
            $('#rate_hint').html(
                '<i class="fas fa-info-circle text-info"></i> Daily rate: <strong>'
                + lastRate.toFixed(2) + '</strong> &middot; Outstanding: <strong>'
                + lastBalance.toFixed(2) + '</strong>'
            );

            // Auto-populate amount if empty (or recompute based on days_paid)
            $('#amount_paid').data('touched', false);
            recalcAmount();
        });
    });

    // Mark amount as touched when user manually edits it
    $('#amount_paid').on('input', function() {
        $(this).data('touched', true);
    });

    // Recompute suggested amount when days change
    $('#days_paid').on('input', function() {
        recalcAmount();
    });

    // Trigger initial fetch if patient is pre-selected (e.g. ?patient_id=)
    @if($patient)
        $('#patient_id').trigger('change');
    @endif
</script>
@stop
