@extends('adminlte::page')

@section('title', 'Pay Caregiver')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pay Caregiver</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('caregiver-payments.index') }}">Caregiver Payments</a></li>
                    <li class="breadcrumb-item active">Pay</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Caregiver Payment Form</h3>
                    </div>
                    <form method="POST" action="{{ route('caregiver-payments.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="caregiver_id">Caregiver *</label>
                                        <select class="form-control @error('caregiver_id') is-invalid @enderror" id="caregiver_id" name="caregiver_id" required>
                                            <option value="">Select Caregiver</option>
                                            @foreach($caregivers as $cg)
                                                <option value="{{ $cg->id }}" data-rate="{{ $cg->monthly_rate }}" {{ old('caregiver_id', $caregiver?->id) == $cg->id ? 'selected' : '' }}>
                                                    {{ $cg->name }} — {{ number_format($cg->monthly_rate, 2) }}/month
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('caregiver_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payment_date">Payment Date *</label>
                                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                        @error('payment_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="period_start">Period Start *</label>
                                        <input type="date" class="form-control @error('period_start') is-invalid @enderror" id="period_start" name="period_start" value="{{ old('period_start', now()->startOfMonth()->format('Y-m-d')) }}" required>
                                        @error('period_start')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="period_end">Period End *</label>
                                        <input type="date" class="form-control @error('period_end') is-invalid @enderror" id="period_end" name="period_end" value="{{ old('period_end', now()->endOfMonth()->format('Y-m-d')) }}" required>
                                        @error('period_end')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount_paid">Amount Paid *</label>
                                        <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" value="{{ old('amount_paid') }}" required>
                                        <small class="text-muted" id="rate_hint">Select a caregiver to auto-fill the monthly rate.</small>
                                        @error('amount_paid')
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

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i> Record &amp; Print Receipt</button>
                            <a href="{{ route('caregiver-payments.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            @if($caregiver)
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Caregiver Summary</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <tr><th>Name</th><td>{{ $caregiver->name }}</td></tr>
                            <tr><th>Phone</th><td>{{ $caregiver->phone }}</td></tr>
                            <tr><th>Gender</th><td>{{ ucfirst($caregiver->gender) }}</td></tr>
                            <tr><th>Date of Entry</th><td>{{ $caregiver->date_of_entry->format('Y-m-d') }}</td></tr>
                            <tr>
                                <th>Payment Plan</th>
                                <td>
                                    @if($caregiver->payment_plan === 'monthly')
                                        <span class="badge badge-primary"><i class="fas fa-calendar-alt mr-1"></i>Monthly</span>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-calendar-day mr-1"></i>Daily</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Pay Rate</th>
                                <td class="font-weight-bold text-info">
                                    {{ number_format((float) $caregiver->monthly_rate, 2) }}
                                    <small class="text-muted">/ {{ $caregiver->payment_plan === 'monthly' ? 'month' : 'day' }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Effective Daily Rate</th>
                                <td>{{ number_format((float) $caregiver->daily_rate, 2) }}</td>
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
    var lastRate = 0;
    var lastPlan = 'daily';

    function recalcAmount() {
        var days = parseInt($('#days_paid').val()) || 1;
        var suggested = lastPlan === 'monthly' ? lastRate : (lastRate * days);
        suggested = parseFloat(suggested).toFixed(2);
        if (!$('#amount_paid').data('touched')) {
            $('#amount_paid').val(suggested);
        }
    }

    $('#caregiver_id').on('change', function() {
        var caregiverId = $(this).val();
        if (!caregiverId) {
            lastRate = 0;
            lastPlan = 'daily';
            $('#rate_hint').text('Select a caregiver to auto-fill the rate.');
            return;
        }

        $.get('/caregiver-payments/caregiver-rate/' + caregiverId, function(data) {
            lastRate = parseFloat(data.rate);
            lastPlan = data.payment_plan;
            $('#rate_hint').html(
                '<i class="fas fa-info-circle text-info"></i> ' +
                'Plan: <strong>' + (lastPlan === 'monthly' ? 'Monthly' : 'Daily') + '</strong> &middot; ' +
                'Rate: <strong>' + lastRate.toFixed(2) + '</strong> / ' +
                (lastPlan === 'monthly' ? 'month' : 'day') +
                ' &middot; Daily equivalent: <strong>' + parseFloat(data.daily_rate).toFixed(2) + '</strong>'
            );

            if (!$('#amount_paid').data('touched')) {
                $('#amount_paid').data('touched', false);
            }
            recalcAmount();
        });
    });

    // Mark amount as touched when user manually edits it
    $('#amount_paid').on('input', function() {
        $(this).data('touched', true);
    });

    // Recompute suggested amount when days change (only for daily caregivers)
    $('#days_paid').on('input', function() {
        recalcAmount();
    });

    // Trigger initial fetch if caregiver is pre-selected
    @if($caregiver)
        $('#caregiver_id').trigger('change');
    @endif
</script>
@stop
