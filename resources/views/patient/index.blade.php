@extends('adminlte::page')

@section('title', 'Patients')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Patients</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Patients</li>
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
                        <h3 class="card-title">Patients List</h3>
                        <div class="card-tools">
                            <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Patient
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('patients.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name, phone..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="on_ward" {{ request('status') == 'on_ward' ? 'selected' : '' }}>On Ward</option>
                                        <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                        <option value="discharged" {{ request('status') == 'discharged' ? 'selected' : '' }}>Discharged</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
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
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Ward</th>
                                    <th>Amount</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Date of Admission</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patients as $patient)
                                    <tr>
                                        <td>{{ $patient->id }}</td>
                                        <td>{{ $patient->name }}</td>
                                        <td>{{ $patient->phone }}</td>
                                        <td>{{ $patient->ward ?? 'N/A' }}</td>
                                        <td>{{ number_format($patient->amount_to_pay, 2) }}</td>
                                        <td>{{ ucfirst($patient->gender) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $patient->patient_status === 'on_ward' ? 'primary' : ($patient->patient_status === 'transferred' ? 'warning' : 'success') }}">
                                                {{ $patient->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $patient->date_of_admission->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No patients found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $patients->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
