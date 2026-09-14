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

@push('css')
<style>
    .calculation-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        color: white;
        padding: 20px;
        height: 100%;
    }
    .calculation-box .label {
        font-size: 0.8rem;
        opacity: 0.9;
        text-transform: uppercase;
    }
    .calculation-box .value {
        font-size: 1.1rem;
        font-weight: 700;
    }
    .calculation-box .due-amount {
        font-size: 1.8rem;
        font-weight: 800;
    }
    .calculation-box .calc-row {
        border-bottom: 1px solid rgba(255,255,255,0.2);
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    .calculation-box .calc-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Caregiver Payment Form</h3>
                    </div>
                    <form method="POST" action="{{ route('caregiver-payments.store') }}" id="paymentForm">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="caregiver_id">Caregiver *</label>
                                        @include('partials.caregiver-select')
                                        @error('caregiver_id')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
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
                                        <label for="amount_paid">Amount Paid *</label>
                                        <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" value="{{ old('amount_paid') }}" required>
                                        <small class="text-muted" id="rate_hint">Select a caregiver to see balance.</small>
                                        @error('amount_paid')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="calculation-box" id="calculationBox" style="display: none;">
                                        <div class="calc-row">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="label">Unpaid Days</span>
                                                <span class="value" id="unpaidDaysDisplay">-</span>
                                            </div>
                                        </div>
                                        <div class="calc-row">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="label">Daily Rate</span>
                                                <span class="value" id="rateDisplay">-</span>
                                            </div>
                                        </div>
                                        <div class="calc-row">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="label">Total Balance</span>
                                                <span class="value due-amount" id="balanceDisplay">UGX 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
                                    {{ number_format((float) $caregiver->monthly_rate, 0) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Effective Daily Rate</th>
                                <td>{{ number_format((float) $caregiver->daily_rate, 0) }}</td>
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
    var currentDailyRate = 0;
    var currentUnpaidDays = 0;
    var currentBalance = 0;
    var amountTouched = false;

    function fetchBalance(caregiverId) {
        if (!caregiverId) {
            currentDailyRate = 0;
            currentUnpaidDays = 0;
            currentBalance = 0;
            $('#calculationBox').hide();
            $('#rate_hint').text('Select a caregiver to see balance.');
            return;
        }

        $.getJSON('{{ route('caregiver-payments.caregiver-rate', ['caregiverId' => 'CGIDPLACEHOLDER']) }}'.replace('CGIDPLACEHOLDER', caregiverId), function(data) {
            currentDailyRate = parseFloat(data.daily_rate) || 0;
            currentUnpaidDays = parseInt(data.unpaid_days) || 0;
            currentBalance = parseFloat(data.total_balance) || 0;

            if (currentBalance > 0) {
                $('#unpaidDaysDisplay').text(currentUnpaidDays + ' day(s)');
                $('#rateDisplay').text('UGX ' + currentDailyRate.toLocaleString() + '/day');
                $('#balanceDisplay').text('UGX ' + Math.round(currentBalance).toLocaleString());
                $('#calculationBox').show();

                if (!amountTouched) {
                    $('#amount_paid').val(Math.round(currentBalance));
                }
                $('#rate_hint').html('<i class="fas fa-check-circle text-success"></i> Balance: <strong>UGX ' + Math.round(currentBalance).toLocaleString() + '</strong> for ' + currentUnpaidDays + ' unpaid day(s).');
            } else {
                $('#calculationBox').hide();
                $('#rate_hint').text('No unpaid balance for this caregiver.');
                if (!amountTouched) {
                    $('#amount_paid').val('');
                }
            }
        }).fail(function() {
            $('#calculationBox').hide();
            $('#rate_hint').text('Could not load balance.');
        });
    }

    $(document).ready(function() {
        // Listen for caregiver selection from the search widget
        $(document).on('caregiverSelected', function(e, id, rate, dailyRate, paymentPlan) {
            var $widgetInput = $('#caregiver_id_widget');
            $widgetInput.val(id);
            $widgetInput.data('rate', rate || 0);
            $widgetInput.data('daily-rate', dailyRate || rate || 0);
            $widgetInput.data('payment-plan', paymentPlan || 'daily');
            fetchBalance(id);
        });

        $(document).on('caregiverDeselected', function(e, oldId) {
            var $widgetInput = $('#caregiver_id_widget');
            $widgetInput.val('');
            $widgetInput.data('rate', 0);
            $widgetInput.data('daily-rate', 0);
            $widgetInput.data('payment-plan', 'daily');
            fetchBalance('');
        });

        // Load initial data if caregiver is pre-selected
        @if($caregiver)
            var preId = '{{ $caregiver->id }}';
            var $widgetInput = $('#caregiver_id_widget');
            $widgetInput.val(preId);
            $widgetInput.data('rate', '{{ $caregiver->monthly_rate }}');
            $widgetInput.data('daily-rate', '{{ $caregiver->daily_rate }}');
            $widgetInput.data('payment-plan', '{{ $caregiver->payment_plan ?? 'daily' }}');

            var $widget = $('.caregiver-select-widget');
            var $chip = $widget.find('.caregiver-chip');
            $chip.find('.caregiver-chip-name').text('{{ $caregiver->name }}');
            $chip.data('id', preId);
            $chip.data('rate', '{{ $caregiver->monthly_rate }}');
            $chip.data('daily-rate', '{{ $caregiver->daily_rate }}');
            $chip.data('payment-plan', '{{ $caregiver->payment_plan ?? 'daily' }}');
            $chip.removeClass('d-none');
            $widget.find('.caregiver-empty-hint').addClass('d-none');

            fetchBalance(preId);
        @endif

        // When caregiver changes (original dropdown)
        $('#caregiver_id').on('change', function() {
            fetchBalance($(this).val());
        });

        // When amount_paid is manually edited
        $('#amount_paid').on('input', function() {
            amountTouched = true;
        });
    });
</script>
@stop
