@extends('adminlte::page')

@section('title', 'Edit Patient')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Patient</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
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
                        <h3 class="card-title">Patient Information</h3>
                    </div>
                    <form method="POST" action="{{ route('patients.update', $patient->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $patient->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="phone">Phone Number *</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" required>
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gender">Gender *</label>
                                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option value="">Select</option>
                                            <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address *</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" required>{{ old('address', $patient->address) }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ward">Ward</label>
                                        <input type="text" class="form-control" id="ward" name="ward" value="{{ old('ward', $patient->ward) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="relative_name">Relative Name</label>
                                        <input type="text" class="form-control" id="relative_name" name="relative_name" value="{{ old('relative_name', $patient->relative_name) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_of_admission">Date of Admission *</label>
                                        <input type="date" class="form-control @error('date_of_admission') is-invalid @enderror" id="date_of_admission" name="date_of_admission" value="{{ old('date_of_admission', $patient->date_of_admission->format('Y-m-d')) }}" required>
                                        @error('date_of_admission')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="amount_to_pay">Amount to Pay (Daily) *</label>
                                        <input type="number" step="0.01" class="form-control @error('amount_to_pay') is-invalid @enderror" id="amount_to_pay" name="amount_to_pay" value="{{ old('amount_to_pay', $patient->amount_to_pay) }}" required>
                                        @error('amount_to_pay')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                            <option value="on_ward" {{ old('status', $patient->status) == 'on_ward' ? 'selected' : '' }}>On Ward</option>
                                            <option value="transferred" {{ old('status', $patient->status) == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                            <option value="discharged" {{ old('status', $patient->status) == 'discharged' ? 'selected' : '' }}>Discharged</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="caregiver_ids">Assign Caregivers</label>
                                        <select class="form-control" id="caregiver_ids" name="caregiver_ids[]" multiple>
                                            @foreach($caregivers as $caregiver)
                                                <option value="{{ $caregiver->id }}" {{ in_array($caregiver->id, $selectedCaregivers) ? 'selected' : '' }}>{{ $caregiver->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_of_discharge">Date of Discharge</label>
                                        <input type="date" class="form-control" id="date_of_discharge" name="date_of_discharge" value="{{ old('date_of_discharge', $patient->date_of_discharge?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_of_transfer">Date of Transfer</label>
                                        <input type="date" class="form-control" id="date_of_transfer" name="date_of_transfer" value="{{ old('date_of_transfer', $patient->date_of_transfer?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discharge_notes">Discharge Notes</label>
                                        <textarea class="form-control" id="discharge_notes" name="discharge_notes" rows="2">{{ old('discharge_notes', $patient->discharge_notes) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="transfer_notes">Transfer Notes</label>
                                        <textarea class="form-control" id="transfer_notes" name="transfer_notes" rows="2">{{ old('transfer_notes', $patient->transfer_notes) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5>Next of Kin Information</h5>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="next_of_kin_name">Next of Kin Name *</label>
                                        <input type="text" class="form-control @error('next_of_kin_name') is-invalid @enderror" id="next_of_kin_name" name="next_of_kin_name" value="{{ old('next_of_kin_name', $patient->next_of_kin_name) }}" required>
                                        @error('next_of_kin_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="next_of_kin_relationship">Relationship *</label>
                                        <input type="text" class="form-control @error('next_of_kin_relationship') is-invalid @enderror" id="next_of_kin_relationship" name="next_of_kin_relationship" value="{{ old('next_of_kin_relationship', $patient->next_of_kin_relationship) }}" required>
                                        @error('next_of_kin_relationship')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="next_of_kin_phone">Next of Kin Phone *</label>
                                        <input type="text" class="form-control @error('next_of_kin_phone') is-invalid @enderror" id="next_of_kin_phone" name="next_of_kin_phone" value="{{ old('next_of_kin_phone', $patient->next_of_kin_phone) }}" required>
                                        @error('next_of_kin_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="next_of_kin_address">Next of Kin Address *</label>
                                        <input type="text" class="form-control @error('next_of_kin_address') is-invalid @enderror" id="next_of_kin_address" name="next_of_kin_address" value="{{ old('next_of_kin_address', $patient->next_of_kin_address) }}" required>
                                        @error('next_of_kin_address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update Patient</button>
                            <a href="{{ route('patients.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
