@extends('adminlte::page')

@section('title', 'Add Expense')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add Expense</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                    <li class="breadcrumb-item active">Add</li>
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
                        <h3 class="card-title">Expense Form</h3>
                    </div>
                    <form method="POST" action="{{ route('expenses.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="description">Description *</label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" required placeholder="e.g., Medical supplies">
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Amount *</label>
                                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required>
                                        @error('amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_date">Date *</label>
                                        <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" value="{{ old('expense_date', now()->format('Y-m-d')) }}" required>
                                        @error('expense_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="category">Category *</label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Save Expense</button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
