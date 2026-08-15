@extends('adminlte::page')

@section('title', 'Patient Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Patient Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item active">{{ $patient->name }}</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <h3 class="profile-username text-center">{{ $patient->name }}</h3>
                        <p class="text-muted text-center">{{ ucfirst($patient->gender) }} | {{ $patient->phone }}</p>
                        <p class="text-center">
                            <span class="badge badge-{{ $patient->patient_status === 'on_ward' ? 'primary' : ($patient->patient_status === 'transferred' ? 'warning' : 'success') }}" style="font-size: 14px;">
                                {{ $patient->status_label }}
                            </span>
                        </p>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Ward</b> <a class="float-right">{{ $patient->ward ?? 'N/A' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Daily Amount</b> <a class="float-right">{{ number_format($patient->amount_to_pay, 2) }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Date of Admission</b> <a class="float-right">{{ $patient->date_of_admission->format('Y-m-d') }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Discharge Date</b> <a class="float-right">{{ $patient->date_of_discharge?->format('Y-m-d') ?? 'N/A' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Transfer Date</b> <a class="float-right">{{ $patient->date_of_transfer?->format('Y-m-d') ?? 'N/A' }}</a>
                            </li>
                        </ul>
                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-primary btn-block"><i class="fas fa-edit"></i> Edit</a>
                    </div>
                </div>

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Address</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ $patient->address }}</p>
                        @if($patient->relative_name)
                            <hr>
                            <p><b>Relative:</b> {{ $patient->relative_name }}</p>
                        @endif
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
                                <td>{{ $patient->next_of_kin_name }}</td>
                            </tr>
                            <tr>
                                <th>Relationship</th>
                                <td>{{ $patient->next_of_kin_relationship }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $patient->next_of_kin_phone }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $patient->next_of_kin_address }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($patient->discharge_notes)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Discharge Notes</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $patient->discharge_notes }}</p>
                    </div>
                </div>
                @endif

                @if($patient->transfer_notes)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transfer Notes</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $patient->transfer_notes }}</p>
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Assigned Caregivers ({{ $patient->caregivers->count() }})</h3>
                    </div>
                    <div class="card-body">
                        @if($patient->caregivers->count() > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->caregivers as $caregiver)
                                        <tr>
                                            <td>{{ $caregiver->id }}</td>
                                            <td>{{ $caregiver->name }}</td>
                                            <td>{{ $caregiver->phone }}</td>
                                            <td>
                                                <a href="{{ route('caregivers.show', $caregiver->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                                <form action="{{ route('patients.remove-caregiver', [$patient->id, $caregiver->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this caregiver?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No caregivers assigned.</p>
                        @endif

                        @if($availableCaregivers->count() > 0)
                            <hr>
                            <h5>Assign New Caregiver</h5>
                            <form action="{{ route('patients.assign-caregiver', $patient->id) }}" method="POST" class="form-inline">
                                @csrf
                                <div class="form-group mr-2">
                                    <select name="caregiver_id" class="form-control" required>
                                        <option value="">Select Caregiver</option>
                                        @foreach($availableCaregivers as $caregiver)
                                            <option value="{{ $caregiver->id }}">{{ $caregiver->name }} - {{ $caregiver->phone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Assign</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
