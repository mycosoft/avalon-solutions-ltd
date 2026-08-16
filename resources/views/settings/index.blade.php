@extends('adminlte::page')

@section('title', 'System Settings')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">System Settings</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Company Information --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building mr-2"></i>Company Information</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3"><small>These details appear on receipts and throughout the system.</small></p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_name">Company Name *</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $grouped['company']['company_name'] ?? 'Avalon Solutions') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_tagline">Tagline</label>
                                <input type="text" class="form-control" id="company_tagline" name="company_tagline" value="{{ old('company_tagline', $grouped['company']['company_tagline'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="company_address">Address</label>
                                <textarea class="form-control" id="company_address" name="company_address" rows="2">{{ old('company_address', $grouped['company']['company_address'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_phone">Phone</label>
                                <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{ old('company_phone', $grouped['company']['company_phone'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_email">Email</label>
                                <input type="email" class="form-control" id="company_email" name="company_email" value="{{ old('company_email', $grouped['company']['company_email'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Finance Settings --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-coins mr-2"></i>Finance &amp; Currency</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_symbol">Currency Symbol</label>
                                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $grouped['finance']['currency_symbol'] ?? 'UGX') }}" placeholder="UGX, $, ₦, KSh...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_code">Currency Code (ISO)</label>
                                <input type="text" class="form-control" id="currency_code" name="currency_code" value="{{ old('currency_code', $grouped['finance']['currency_code'] ?? 'UGX') }}" maxlength="3">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="default_payment_method">Default Payment Method</label>
                                <select class="form-control" id="default_payment_method" name="default_payment_method">
                                    @php $dpm = old('default_payment_method', $grouped['finance']['default_payment_method'] ?? 'cash'); @endphp
                                    <option value="cash"        {{ $dpm == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank"        {{ $dpm == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="mobile_money" {{ $dpm == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                    <option value="other"       {{ $dpm == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="low_balance_threshold">Low Balance Alert Threshold</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ $grouped['finance']['currency_symbol'] ?? 'UGX' }}</span>
                                    </div>
                                    <input type="number" step="0.01" class="form-control" id="low_balance_threshold" name="low_balance_threshold" value="{{ old('low_balance_threshold', $grouped['finance']['low_balance_threshold'] ?? '50000') }}">
                                </div>
                                <small class="text-muted">Accountants are notified when a patient's balance falls below this amount.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Receipt Settings --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-receipt mr-2"></i>POS Receipt Settings</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="receipt_footer">Receipt Footer Message</label>
                                <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="2">{{ old('receipt_footer', $grouped['receipt']['receipt_footer'] ?? 'Thank you for your payment!') }}</textarea>
                                <small class="text-muted">Printed at the bottom of every receipt.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input type="hidden" name="receipt_show_logo" value="0">
                                    <input type="checkbox" class="form-check-input" id="receipt_show_logo" name="receipt_show_logo" value="1" {{ ($grouped['receipt']['receipt_show_logo'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="receipt_show_logo">Show company logo on receipt header</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i> Save Settings</button>
                <a href="{{ route('dashboard') }}" class="btn btn-default ml-2">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
    $('#currency_symbol').on('input', function() {
        $('.input-group-prepend .input-group-text').first().text($(this).val() || 'UGX');
    });
</script>
@stop
