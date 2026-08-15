@extends('adminlte::page')

@section('title', 'Edit Attendance')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Attendance</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Attendance Record</h3>
                    </div>
                    <form method="POST" action="{{ route('attendance.update', $attendance->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date">Date *</label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $attendance->date->format('Y-m-d')) }}" required>
                                        @error('date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="caregiver_id">Caregiver *</label>
                                        <select class="form-control @error('caregiver_id') is-invalid @enderror" id="caregiver_id" name="caregiver_id" required>
                                            <option value="">Select Caregiver</option>
                                            @foreach($caregivers as $cg)
                                                <option value="{{ $cg->id }}" {{ old('caregiver_id', $attendance->caregiver_id) == $cg->id ? 'selected' : '' }}>{{ $cg->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('caregiver_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="patient_id">Patient *</label>
                                        <select class="form-control @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                            <option value="">Select Patient</option>
                                            @foreach($patients as $pt)
                                                <option value="{{ $pt->id }}" {{ old('patient_id', $attendance->patient_id) == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('patient_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ward">Ward</label>
                                        <input type="text" class="form-control" id="ward" name="ward" value="{{ old('ward', $attendance->ward) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="days_under_care">Days Under Care</label>
                                        <input type="number" class="form-control" id="days_under_care" name="days_under_care" value="{{ old('days_under_care', $attendance->days_under_care) }}" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="admin_observation">Admin Observation</label>
                                <textarea class="form-control" id="admin_observation" name="admin_observation" rows="3">{{ old('admin_observation', $attendance->admin_observation) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="complaint_reported">Complaint or Issues Reported</label>
                                <textarea class="form-control" id="complaint_reported" name="complaint_reported" rows="3">{{ old('complaint_reported', $attendance->complaint_reported) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="complaint_assignment">Complaint Assignment</label>
                                <textarea class="form-control" id="complaint_assignment" name="complaint_assignment" rows="3">{{ old('complaint_assignment', $attendance->complaint_assignment) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="follow_up">Follow Up</label>
                                <textarea class="form-control" id="follow_up" name="follow_up" rows="3">{{ old('follow_up', $attendance->follow_up) }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('attendance.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
