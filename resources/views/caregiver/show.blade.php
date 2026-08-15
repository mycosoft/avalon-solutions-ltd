@extends('adminlte::page')

@section('title', 'Caregiver Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Caregiver Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('caregivers.index') }}">Caregivers</a></li>
                    <li class="breadcrumb-item active">{{ $caregiver->name }}</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if($caregiver->photo)
                                <img class="profile-user-img img-fluid img-circle" src="{{ Storage::url($caregiver->photo) }}" alt="Photo">
                            @else
                                <img class="profile-user-img img-fluid img-circle" src="{{ asset('images/logo.png') }}" alt="No Photo">
                            @endif
                        </div>
                        <h3 class="profile-username text-center">{{ $caregiver->name }}</h3>
                        <p class="text-muted text-center">{{ ucfirst($caregiver->gender) }} | {{ $caregiver->phone }}</p>
                        <p class="text-center">
                            @if($caregiver->status)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </p>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>NIN</b> <a class="float-right">{{ $caregiver->nin }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Date of Birth</b> <a class="float-right">{{ $caregiver->date_of_birth->format('Y-m-d') }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Date of Entry</b> <a class="float-right">{{ $caregiver->date_of_entry->format('Y-m-d') }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Level of Education</b> <a class="float-right">{{ $caregiver->level_of_education ?? 'N/A' }}</a>
                            </li>
                        </ul>
                        <a href="{{ route('caregivers.edit', $caregiver->id) }}" class="btn btn-primary btn-block"><i class="fas fa-edit"></i> Edit</a>
                    </div>
                </div>

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Address</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ $caregiver->address }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Next of Kin</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="200">Name</th>
                                <td>{{ $caregiver->next_of_kin_name }}</td>
                            </tr>
                            <tr>
                                <th>Relationship</th>
                                <td>{{ $caregiver->next_of_kin_relationship }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $caregiver->next_of_kin_phone }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $caregiver->next_of_kin_address }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Assigned Patients ({{ $caregiver->patients->count() }})</h3>
                    </div>
                    <div class="card-body">
                        @if($caregiver->patients->count() > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Ward</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($caregiver->patients as $patient)
                                        <tr>
                                            <td>{{ $patient->id }}</td>
                                            <td>{{ $patient->name }}</td>
                                            <td>{{ $patient->ward ?? 'N/A' }}</td>
                                            <td><span class="badge badge-{{ $patient->patient_status === 'on_ward' ? 'primary' : ($patient->patient_status === 'transferred' ? 'warning' : 'success') }}">{{ $patient->status_label }}</span></td>
                                            <td>
                                                <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No patients assigned to this caregiver.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
