<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #212529; }
        .header { border-bottom: 3px solid #343a40; padding-bottom: 8px; margin-bottom: 12px; }
        .company { font-size: 18px; font-weight: bold; color: #343a40; }
        .company-sub { font-size: 11px; color: #6c757d; }
        .report-title { font-size: 15px; font-weight: bold; margin-top: 6px; }
        .meta { font-size: 10px; color: #6c757d; margin-top: 2px; }
        .filters { margin: 8px 0 12px 0; }
        .filter-item { display: inline-block; background: #e9ecef; padding: 2px 8px; border-radius: 3px; margin-right: 6px; font-size: 10px; }
        .totals { margin-bottom: 12px; }
        .total-box { display: inline-block; border: 1px solid #dee2e6; padding: 6px 14px; margin-right: 8px; border-radius: 4px; }
        .total-label { font-size: 9px; color: #6c757d; text-transform: uppercase; }
        .total-value { font-size: 14px; font-weight: bold; color: #343a40; }
        .section { margin-bottom: 16px; }
        .section-title { font-size: 12px; font-weight: bold; background: #343a40; color: #fff; padding: 4px 8px; border-radius: 3px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dee2e6; padding: 4px 6px; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) td { background: #f8f9fa; }
        .empty { text-align: center; color: #6c757d; padding: 12px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; color: #6c757d; text-align: center; border-top: 1px solid #dee2e6; padding-top: 4px; }
        .page-number:after { content: counter(page) " / " counter(pages); }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">Avalon Solutions Ltd</div>
        <div class="company-sub">Caregiver &amp; Patient Management System</div>
        <div class="report-title">{{ $report['title'] }}</div>
        <div class="meta">{{ $report['description'] }} &mdash; Generated {{ $report['generated_at'] }}</div>
    </div>

    @if(count($report['filters']) > 0)
        <div class="filters">
            @foreach($report['filters'] as $filter)
                <span class="filter-item"><strong>{{ $filter['label'] }}:</strong> {{ $filter['value'] }}</span>
            @endforeach
        </div>
    @endif

    @if(count($report['totals']) > 0)
        <div class="totals">
            @foreach($report['totals'] as $total)
                <div class="total-box">
                    <div class="total-label">{{ $total['label'] }}</div>
                    <div class="total-value">{{ $total['value'] }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @foreach($report['sections'] as $section)
        <div class="section">
            <div class="section-title">{{ $section['title'] }}</div>
            @if(empty($section['rows']))
                <table><tr><td class="empty">{{ $section['empty'] ?? 'No records found.' }}</td></tr></table>
            @else
                <table>
                    <thead>
                        <tr>
                            @foreach($section['headers'] as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($section['rows'] as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

    <div class="footer">
        Avalon Solutions Ltd &bull; Confidential &bull; Page <span class="page-number"></span>
    </div>
</body>
</html>