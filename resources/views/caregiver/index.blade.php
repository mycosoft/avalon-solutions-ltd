@extends('adminlte::page')

@section('title', 'Caregivers')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Caregivers</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Caregivers</li>
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
                        <h3 class="card-title">Caregivers List</h3>
                        <div class="card-tools">
                            <a href="{{ route('caregivers.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Caregiver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('caregivers.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name, phone, NIN..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
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
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>NIN</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Date of Entry</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($caregivers as $caregiver)
                                    <tr>
                                        <td>{{ $caregiver->id }}</td>
                                        <td>
                                            @if($caregiver->photo)
                                                <img src="{{ Storage::url($caregiver->photo) }}" alt="Photo" class="img-circle" width="40" height="40">
                                            @else
                                                <img src="{{ asset('images/logo.png') }}" alt="No Photo" class="img-circle" width="40" height="40">
                                            @endif
                                        </td>
                                        <td>{{ $caregiver->name }}</td>
                                        <td>{{ $caregiver->phone }}</td>
                                        <td>{{ $caregiver->nin }}</td>
                                        <td>{{ ucfirst($caregiver->gender) }}</td>
                                        <td>
                                            @if($caregiver->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $caregiver->date_of_entry->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('caregivers.show', $caregiver->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('caregivers.edit', $caregiver->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('caregivers.destroy', $caregiver->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No caregivers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $caregivers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
