@extends('adminlte::page')

@section('title', $report['title'])

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $report['title'] }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active">{{ $report['title'] }}</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $report['description'] }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('reports.export.pdf', $report['type']) . '?' . http_build_query(request()->query()) }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('reports.export.excel', $report['type']) . '?' . http_build_query(request()->query()) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(! empty($filterForm))
                            <form method="GET" action="{{ route('reports.show', $report['type']) }}" class="mb-4">
                                <div class="row">
                                    @foreach($filterForm as $field)
                                        <div class="col-md-3 mb-2">
                                            @if($field['type'] === 'select')
                                                <select name="{{ $field['name'] }}" class="form-control">
                                                    <option value="">{{ $field['placeholder'] }}</option>
                                                    @foreach($field['options'] as $value => $label)
                                                        <option value="{{ $value }}" {{ (string) $field['value'] === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" class="form-control" value="{{ $field['value'] }}" placeholder="{{ $field['label'] }}">
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="col-md-3 mb-2">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-filter"></i> Apply Filters
                                        </button>
                                        <a href="{{ route('reports.show', $report['type']) }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        @endif

                        @if(count($report['totals']) > 0)
                            <div class="row mb-4">
                                @foreach($report['totals'] as $total)
                                    <div class="col-lg-3 col-md-4 col-6 mb-2">
                                        <div class="info-box">
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ $total['label'] }}</span>
                                                <span class="info-box-number">{{ $total['value'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @foreach($report['sections'] as $section)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">{{ $section['title'] }}</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead class="thead-dark">
                                                <tr>
                                                    @foreach($section['headers'] as $header)
                                                        <th>{{ $header }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($section['rows'] as $row)
                                                    <tr>
                                                        @foreach($row as $cell)
                                                            <td>{{ $cell }}</td>
                                                        @endforeach
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ count($section['headers']) }}" class="text-center">
                                                            {{ $section['empty'] ?? 'No records found.' }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-muted">
                            <small>Generated on {{ $report['generated_at'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop