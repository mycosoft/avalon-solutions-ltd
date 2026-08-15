@extends('adminlte::page')

@section('title', 'Edit Caregiver')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Caregiver</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('caregivers.index') }}">Caregivers</a></li>
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
                        <h3 class="card-title">Caregiver Information</h3>
                    </div>
                    <form method="POST" action="{{ route('caregivers.update', $caregiver->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $caregiver->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="photo">Photo</label>
                                        @if($caregiver->photo)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($caregiver->photo) }}" alt="Current Photo" class="img-circle" width="60" height="60">
                                                <span class="ml-2 text-muted">Current photo</span>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                        @error('photo')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number *</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $caregiver->phone) }}" required>
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nin">NIN (National ID) *</label>
                                        <input type="text" class="form-control @error('nin') is-invalid @enderror" id="nin" name="nin" value="{{ old('nin', $caregiver->nin) }}" required>
                                        @error('nin')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address *</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" required>{{ old('address', $caregiver->address) }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date of Birth *</label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $caregiver->date_of_birth->format('Y-m-d')) }}" required>
                                        @error('date_of_birth')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gender">Gender *</label>
                                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option value="">Select</option>
                                            <option value="male" {{ old('gender', $caregiver->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $caregiver->gender) == 'female' ? 'selected' : '' }}>Female</option>
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
                                        <label for="level_of_education">Level of Education</label>
                                        <input type="text" class="form-control" id="level_of_education" name="level_of_education" value="{{ old('level_of_education', $caregiver->level_of_education) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_entry">Date of Entry (Admission) *</label>
                                        <input type="date" class="form-control @error('date_of_entry') is-invalid @enderror" id="date_of_entry" name="date_of_entry" value="{{ old('date_of_entry', $caregiver->date_of_entry->format('Y-m-d')) }}" required>
                                        @error('date_of_entry')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5>Next of Kin Information</h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="next_of_kin_name">Next of Kin Name *</label>
                                        <input type="text" class="form-control @error('next_of_kin_name') is-invalid @enderror" id="next_of_kin_name" name="next_of_kin_name" value="{{ old('next_of_kin_name', $caregiver->next_of_kin_name) }}" required>
                                        @error('next_of_kin_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="next_of_kin_relationship">Relationship *</label>
                                        <input type="text" class="form-control @error('next_of_kin_relationship') is-invalid @enderror" id="next_of_kin_relationship" name="next_of_kin_relationship" value="{{ old('next_of_kin_relationship', $caregiver->next_of_kin_relationship) }}" required>
                                        @error('next_of_kin_relationship')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="next_of_kin_phone">Next of Kin Phone *</label>
                                        <input type="text" class="form-control @error('next_of_kin_phone') is-invalid @enderror" id="next_of_kin_phone" name="next_of_kin_phone" value="{{ old('next_of_kin_phone', $caregiver->next_of_kin_phone) }}" required>
                                        @error('next_of_kin_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="next_of_kin_address">Next of Kin Address *</label>
                                        <textarea class="form-control @error('next_of_kin_address') is-invalid @enderror" id="next_of_kin_address" name="next_of_kin_address" rows="2" required>{{ old('next_of_kin_address', $caregiver->next_of_kin_address) }}</textarea>
                                        @error('next_of_kin_address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $caregiver->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update Caregiver</button>
                            <a href="{{ route('caregivers.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
