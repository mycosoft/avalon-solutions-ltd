@extends('adminlte::page')

@section('title', 'Expense Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Expense Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
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
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Expense Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="150">ID</th>
                                <td>{{ $expense->id }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $expense->description }}</td>
                            </tr>
                            <tr>
                                <th>Amount</th>
                                <td class="text-danger font-weight-bold">{{ number_format($expense->amount, 0) }}</td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td><span class="badge badge-info">{{ $expense->category }}</span></td>
                            </tr>
                            <tr>
                                <th>Recorded By</th>
                                <td>{{ $expense->recorded_by ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $expense->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                        @if($expense->notes)
                            <hr>
                            <h5>Notes</h5>
                            <p>{{ $expense->notes }}</p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-info"><i class="fas fa-edit"></i> Edit</a>
                        <a href="{{ route('expenses.index') }}" class="btn btn-default ml-2">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
