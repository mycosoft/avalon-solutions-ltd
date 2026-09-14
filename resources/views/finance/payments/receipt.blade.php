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
        /* Mobile Bluetooth printer: 58mm roll */
        @page {
            size: 58mm auto;
            margin: 0;
        }
        * { box-sizing: border-box; }
        body {
            font-size: 13px;
            font-weight: 400;
            line-height: 1.4;
            color: #000;
            background: #f1f3f5;
            margin: 0;
            padding: 18px;
        }
        .receipt {
            width: 54mm;
            margin: 0 auto;
            background: #fff;
            padding: 3mm 1mm;
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
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header .tagline {
            font-size: 11px;
            font-style: italic;
            margin-bottom: 4px;
        }
        .header .meta {
            font-size: 11px;
            line-height: 1.5;
        }
        .logo {
            width: 44px;
            height: 44px;
            margin: 0 auto 6px;
            display: block;
            object-fit: cover;
            border-radius: 50%;
        }
        .row {
            display: table;
            width: 100%;
            font-size: 12px;
        }
        .row > span:first-child {
            display: table-cell;
            text-align: left;
        }
        .row > span:last-child {
            display: table-cell;
            text-align: right;
        }
        .label { font-weight: 400; }
        .receipt-title {
            text-align: center;
            font-weight: 700;
            font-size: 15px;
            margin: 4px 0;
            letter-spacing: 1px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        table.items td {
            padding: 2px 0;
            vertical-align: top;
        }
        table.items td.desc { width: 65%; }
        table.items td.amt  { width: 35%; text-align: right; }
        .period-row {
            padding: 2px 0;
        }
        .period-label {
            font-weight: 700;
            margin-bottom: 1px;
        }
        .period-value {
            font-weight: 400;
            white-space: nowrap;
            display: inline-block;
        }
        .totals .row {
            font-size: 13px;
            padding: 2px 0;
        }
        .totals .grand {
            font-size: 16px;
            font-weight: 400;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin: 4px 0;
        }
        .totals .grand .amount {
            font-weight: 700;
        }
        .footer-note {
            text-align: center;
            font-size: 12px;
            margin-top: 8px;
            line-height: 1.5;
        }
        .footer-note .thanks {
            font-weight: 400;
            font-size: 13px;
            margin-bottom: 4px;
        }
        .actions {
            width: 54mm;
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
            body {
                background: #fff;
                padding: 0;
                font-size: 15px;
                font-weight: 400;
                line-height: 1.3;
            }
            .receipt {
                border: none;
                padding: 3mm 1mm;
                width: 54mm;
            }
            .actions { display: none !important; }
            .header h1 { font-size: 19px; }
            .header .tagline, .header .meta { font-size: 12px; }
            .row { font-size: 13px; }
            table.items { font-size: 13px; }
            .receipt-title { font-size: 16px; }
            .totals .row { font-size: 14px; }
            .totals .grand { font-size: 17px; }
            .footer-note { font-size: 13px; }
            .footer-note .thanks { font-size: 14px; }
        }

        /* PDF (dompdf) mode */
        body.pdf {
            background: #fff;
            padding: 0;
        }
        body.pdf .receipt {
            border: none;
            width: auto;
            margin: 0;
        }
        body.pdf .actions { display: none; }
    </style>
</head>
<body class="{{ ($pdfMode ?? false) ? 'pdf' : '' }}">

<div class="receipt">

    {{-- Header --}}
    <div class="header">
        @if($showLogo)
            <img class="logo" src="{{ ($pdfMode ?? false) ? public_path('images/avalon.jpeg') : asset('images/avalon.jpeg') }}" alt="{{ $companyName }}" onerror="this.style.display='none'">
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
                <td class="desc">Salary for
                    @if($payment->period_start && $payment->period_end)
                    <br>{{ $payment->period_start->format('M d, Y') }} - {{ $payment->period_end->format('M d, Y') }}
                    @endif
                </td>
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
                <td colspan="2" class="period-row">
                    <div class="period-label">Period:</div>
                    <div class="period-value">@if($payment->period_start && $payment->period_end){{ $payment->period_start->format('Y-m-d') }} - {{ $payment->period_end->format('Y-m-d') }}@endif</div>
                </td>
            </tr>
        </table>
        <div class="sep"></div>
    @endif

    {{-- Totals --}}
    <div class="totals">
        <div class="row grand">
            <span>TOTAL PAID</span>
            <span class="amount">{{ $currency }} {{ number_format($payment->amount_paid, 0) }}</span>
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
        <div style="font-size: 11px;">Notes: {{ $payment->notes }}</div>
    @endif

    <div class="sep-thick"></div>
    <div class="footer-note">
        <div class="thanks">{{ $receiptFooter }}</div>
        <div>Recorded by: {{ $payment->recorded_by ?? auth()->user()->name ?? 'System' }}</div>
        <div style="margin-top: 4px;">- {{ strtoupper($companyName) }} -</div>
    </div>

</div>

<div class="actions">
    <button onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    <button onclick="downloadReceiptImage(this)"><i class="fas fa-download"></i> Download</button>
    <a href="{{ $isCaregiver ? route('caregiver-payments.index') : route('payments.index') }}"><i class="fas fa-arrow-left"></i> Back</a>
    <button class="close-btn" onclick="window.close()"><i class="fas fa-times"></i> Close</button>
</div>

<script src="{{ asset('js/html2canvas.min.js') }}?v=2"></script>
<script>
    var receiptImageDataUrl = null;
    var captureInProgress = false;

    function captureReceipt(callback, onError) {
        if (typeof html2canvas === 'undefined') {
            if (onError) onError('missing');
            return;
        }
        if (captureInProgress) {
            if (onError) onError('busy');
            return;
        }
        captureInProgress = true;

        var receipt = document.querySelector('.receipt');

        // Hide the preview border so the image is the clean receipt
        var oldBorder = receipt.style.border;
        var oldRadius = receipt.style.borderRadius;
        receipt.style.border = 'none';
        receipt.style.borderRadius = '0';

        html2canvas(receipt, {
            scale: 3,
            backgroundColor: '#ffffff',
            useCORS: true
        }).then(function (canvas) {
            receipt.style.border = oldBorder;
            receipt.style.borderRadius = oldRadius;
            captureInProgress = false;

            // Pad the capture to 58mm-equivalent width (receipt is 54mm) so
            // apps that stretch the image to paper width keep the true size.
            var padded = document.createElement('canvas');
            padded.width = Math.round(canvas.width * 58 / 54);
            padded.height = canvas.height;
            var ctx = padded.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, padded.width, padded.height);
            ctx.drawImage(canvas, Math.round((padded.width - canvas.width) / 2), 0);

            receiptImageDataUrl = padded.toDataURL('image/png');
            callback(receiptImageDataUrl);
        }).catch(function (error) {
            receipt.style.border = oldBorder;
            receipt.style.borderRadius = oldRadius;
            captureInProgress = false;
            if (onError) onError(error);
        });
    }

    function downloadReceiptImage(btn) {
        var oldLabel = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Preparing...';

        var doDownload = function (dataUrl) {
            var link = document.createElement('a');
            link.download = 'receipt-{{ $payment->receipt_number }}.png';
            link.href = dataUrl;
            link.click();

            btn.disabled = false;
            btn.innerHTML = oldLabel;
        };

        if (receiptImageDataUrl) {
            doDownload(receiptImageDataUrl);
        } else {
            captureReceipt(doDownload, function () {
                btn.disabled = false;
                btn.innerHTML = oldLabel;
                alert('Image library failed to load. Please update the server files (public/js/html2canvas.min.js is missing).');
            });
        }
    }
</script>

</body>
</html>
