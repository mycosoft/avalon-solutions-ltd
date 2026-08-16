@php
    use App\Models\Setting;
    $companyName    = Setting::get('company_name', 'Avalon Solutions');
    $companyAddress = Setting::get('company_address', '');
    $companyPhone   = Setting::get('company_phone', '');
    $companyEmail   = Setting::get('company_email', '');
    $companyTagline = Setting::get('company_tagline', '');
    $currency       = Setting::get('currency_symbol', 'UGX');
    $receiptFooter  = Setting::get('receipt_footer', 'Thank you for your payment!');
    $showLogo       = Setting::get('receipt_show_logo', '1') == '1';

    $isCaregiver = ($payment->payee_for ?? 'patient') === 'caregiver';
    $payeeName   = $isCaregiver ? ($payment->caregiver->name ?? 'N/A') : ($payment->patient->name ?? 'N/A');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $payment->receipt_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Thermal printer: 80mm roll */
        @page {
            size: 80mm auto;
            margin: 0;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #f1f3f5;
            margin: 0;
            padding: 18px;
        }
        .receipt {
            width: 72mm;
            margin: 0 auto;
            background: #fff;
            padding: 6mm 5mm;
            border: 1px solid #d0d4d9;
            border-radius: 2px;
        }
        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: 700; }
        .sep    {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .sep-thick {
            border-top: 1px solid #000;
            margin: 6px 0;
        }
        .header {
            text-align: center;
            margin-bottom: 4px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 2px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }
        .header .tagline {
            font-size: 10px;
            font-style: italic;
            margin-bottom: 4px;
        }
        .header .meta {
            font-size: 10px;
            line-height: 1.5;
        }
        .logo {
            width: 50px;
            height: 50px;
            margin: 0 auto 6px;
            display: block;
            object-fit: contain;
        }
        .row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .label { font-weight: 700; }
        .receipt-title {
            text-align: center;
            font-weight: 900;
            font-size: 13px;
            margin: 4px 0;
            letter-spacing: 1px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        table.items td {
            padding: 2px 0;
            vertical-align: top;
        }
        table.items td.desc { width: 65%; }
        table.items td.amt  { width: 35%; text-align: right; }
        .totals .row {
            font-size: 12px;
            padding: 2px 0;
        }
        .totals .grand {
            font-size: 14px;
            font-weight: 900;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin: 4px 0;
        }
        .footer-note {
            text-align: center;
            font-size: 11px;
            margin-top: 8px;
            line-height: 1.5;
        }
        .footer-note .thanks {
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 4px;
        }
        .actions {
            width: 72mm;
            margin: 14px auto 0;
            text-align: center;
        }
        .actions button,
        .actions a {
            font-family: inherit;
            background: #17a2b8;
            color: #fff;
            border: 0;
            padding: 8px 14px;
            margin: 0 4px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }
        .actions button:hover,
        .actions a:hover { background: #138496; }
        .actions .close-btn { background: #6c757d; }
        .actions .close-btn:hover { background: #5a6268; }

        /* Hide action buttons when printing */
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { border: none; padding: 4mm; width: auto; }
            .actions { display: none !important; }
        }
    </style>
</head>
<body>

<div class="receipt">

    {{-- Header --}}
    <div class="header">
        @if($showLogo)
            <img class="logo" src="{{ asset('images/logo.png') }}" alt="{{ $companyName }}" onerror="this.style.display='none'">
        @endif
        <h1>{{ strtoupper($companyName) }}</h1>
        @if($companyTagline)<div class="tagline">{{ $companyTagline }}</div>@endif
        <div class="meta">
            @if($companyAddress){!! nl2br(e($companyAddress)) !!}<br>@endif
            @if($companyPhone)Tel: {{ $companyPhone }}@endif
            @if($companyPhone && $companyEmail)<br>@endif
            @if($companyEmail)Email: {{ $companyEmail }}@endif
        </div>
    </div>

    <div class="receipt-title">{{ $isCaregiver ? 'PAYROLL RECEIPT' : 'PAYMENT RECEIPT' }}</div>

    {{-- Meta --}}
    <div class="row"><span class="label">Receipt #:</span><span>{{ $payment->receipt_number }}</span></div>
    <div class="row"><span class="label">Date:</span><span>{{ $payment->created_at->format('Y-m-d H:i') }}</span></div>
    <div class="row"><span class="label">Type:</span><span>{{ $isCaregiver ? 'Caregiver Pay' : 'Patient Payment' }}</span></div>

    <div class="sep"></div>

    {{-- Payee --}}
    <div class="row"><span class="label">{{ $isCaregiver ? 'Caregiver:' : 'Patient:' }}</span><span>{{ $payeeName }}</span></div>
    @if($isCaregiver)
        <div class="row"><span class="label">Phone:</span><span>{{ $payment->caregiver->phone ?? '-' }}</span></div>
        <div class="row"><span class="label">NIN:</span><span>{{ $payment->caregiver->nin ?? '-' }}</span></div>
    @else
        <div class="row"><span class="label">Ward:</span><span>{{ $payment->patient->ward ?? '-' }}</span></div>
        <div class="row"><span class="label">Payee:</span><span>{{ $payment->payee_name }}</span></div>
    @endif

    <div class="sep"></div>

    {{-- Items --}}
    @if($isCaregiver)
        <table class="items">
            <tr>
                <td class="desc">Salary for<br>{{ $payment->period_start->format('M d, Y') }} – {{ $payment->period_end->format('M d, Y') }}</td>
                <td class="amt">{{ $currency }} {{ number_format($payment->amount_paid, 0) }}</td>
            </tr>
        </table>
        <div class="sep"></div>
        <div class="row"><span>Monthly Rate</span><span>{{ $currency }} {{ number_format($payment->monthly_rate ?? 0, 0) }}</span></div>
    @else
        <table class="items">
            <tr>
                <td class="desc">Daily Rate</td>
                <td class="amt">{{ $currency }} {{ number_format($payment->daily_rate ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td class="desc">Days Paid</td>
                <td class="amt">{{ $payment->days_paid }}</td>
            </tr>
            <tr>
                <td class="desc">Period<br><small>{{ $payment->period_start->format('Y-m-d') }} → {{ $payment->period_end->format('Y-m-d') }}</small></td>
                <td class="amt"></td>
            </tr>
        </table>
        <div class="sep"></div>
    @endif

    {{-- Totals --}}
    <div class="totals">
        <div class="row grand">
            <span>TOTAL PAID</span>
            <span>{{ $currency }} {{ number_format($payment->amount_paid, 0) }}</span>
        </div>
        @if(!$isCaregiver)
            <div class="row"><span>Method</span><span>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></div>
            <div class="row"><span>Type</span><span>{{ ucfirst($payment->payment_type) }}</span></div>
            <div class="row"><span>Balance</span><span>{{ $currency }} {{ number_format($payment->balance, 0) }}</span></div>
        @else
            <div class="row"><span>Method</span><span>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></div>
        @endif
    </div>

    @if($payment->notes)
        <div class="sep"></div>
        <div style="font-size: 10px;"><strong>Notes:</strong> {{ $payment->notes }}</div>
    @endif

    <div class="sep-thick"></div>
    <div class="footer-note">
        <div class="thanks">{{ $receiptFooter }}</div>
        <div>Recorded by: {{ $payment->recorded_by ?? auth()->user()->name ?? 'System' }}</div>
        <div style="margin-top: 4px;">— {{ strtoupper($companyName) }} —</div>
    </div>

</div>

<div class="actions">
    <button onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    <a href="{{ $isCaregiver ? route('caregiver-payments.index') : route('payments.index') }}"><i class="fas fa-arrow-left"></i> Back</a>
    <button class="close-btn" onclick="window.close()"><i class="fas fa-times"></i> Close</button>
</div>

</body>
</html>
