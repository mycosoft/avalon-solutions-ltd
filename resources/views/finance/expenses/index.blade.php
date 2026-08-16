@extends('adminlte::page')

@section('title', 'Expenses')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Expenses</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
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
                        <h3 class="card-title">Expense Records</h3>
                        <div class="card-tools">
                            <a href="{{ route('expenses.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add Expense
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('expenses.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="category" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-info">Filter</button>
                                </div>
                            </div>
                        </form>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Recorded By</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->id }}</td>
                                        <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td><span class="badge badge-info">{{ $expense->category }}</span></td>
                                        <td>{{ number_format($expense->amount, 0) }}</td>
                                        <td>{{ $expense->recorded_by ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No expenses found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $expenses->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
