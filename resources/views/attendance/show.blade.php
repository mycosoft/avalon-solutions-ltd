@extends('adminlte::page')

@section('title', 'Attendance Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Attendance Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
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
                        <h3 class="card-title">Attendance Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="150">Date</th>
                                <td>{{ $attendance->date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Caregiver</th>
                                <td>{{ $attendance->caregiver->name }}</td>
                            </tr>
                            <tr>
                                <th>Patient</th>
                                <td>{{ $attendance->patient->name }}</td>
                            </tr>
                            <tr>
                                <th>Ward</th>
                                <td>{{ $attendance->ward ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Days Under Care</th>
                                <td>{{ $attendance->days_under_care }} days</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Observations</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="150">Admin Observation</th>
                            </tr>
                            <tr>
                                <td>{{ $attendance->admin_observation ?? 'No observations recorded' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Complaints & Follow-up</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Complaint Reported</th>
                                <td>{{ $attendance->complaint_reported ?? 'None' }}</td>
                            </tr>
                            <tr>
                                <th>Complaint Assignment</th>
                                <td>{{ $attendance->complaint_assignment ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Follow Up</th>
                                <td>{{ $attendance->follow_up ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('attendance.index') }}" class="btn btn-default ml-2">Back</a>
            </div>
        </div>
    </div>
@stop
