@extends('adminlte::page')

@section('title', 'Attendance')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daily Attendance</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
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
                        <h3 class="card-title">Attendance Records</h3>
                        <div class="card-tools">
                            <a href="{{ route('attendance.create') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-plus"></i> New Attendance
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('attendance.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="caregiver_id" class="form-control">
                                        <option value="">All Caregivers</option>
                                        @foreach($caregivers as $cg)
                                            <option value="{{ $cg->id }}" {{ request('caregiver_id') == $cg->id ? 'selected' : '' }}>{{ $cg->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="patient_id" class="form-control">
                                        <option value="">All Patients</option>
                                        @foreach($patients as $pt)
                                            <option value="{{ $pt->id }}" {{ request('patient_id') == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                        @endforeach
                                    </select>
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
                                    <th>Date</th>
                                    <th>Caregiver</th>
                                    <th>Patient</th>
                                    <th>Ward</th>
                                    <th>Days</th>
                                    <th>Complaint</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $att)
                                    <tr>
                                        <td>{{ $att->date->format('Y-m-d') }}</td>
                                        <td>{{ $att->caregiver->name }}</td>
                                        <td>{{ $att->patient->name }}</td>
                                        <td>{{ $att->ward ?? 'N/A' }}</td>
                                        <td>{{ $att->days_under_care }}</td>
                                        <td>
                                            @if($att->complaint_reported)
                                                <span class="badge badge-danger">Yes</span>
                                            @else
                                                <span class="badge badge-success">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('attendance.show', $att->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('attendance.edit', $att->id) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('attendance.destroy', $att->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $attendances->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
