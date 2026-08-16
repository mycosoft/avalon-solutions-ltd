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

@php
    use App\Models\Setting;
    $currency     = Setting::get('currency_symbol', 'UGX');
    $daysAdmitted = $patient->days_admitted;
    $totalDue     = $patient->total_due;
    $totalPaid    = $patient->total_paid;
    $balance      = $patient->balance;
    $daysOwed     = $patient->days_owed;
@endphp

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        {{-- ============================================================
             Two-column layout — stats moved into left sidebar
             ============================================================ --}}
        <div class="row">

            {{-- ============== LEFT SIDEBAR ============== --}}
            <div class="col-md-4">

                {{-- Profile --}}
                <div class="card card-info card-outline">
                    <div class="card-body box-profile">
                        <h3 class="profile-username text-center">{{ $patient->name }}</h3>
                        <p class="text-muted text-center mb-1">
                            {{ ucfirst($patient->gender) }} &middot; {{ $patient->phone }}
                        </p>
                        <p class="text-center">
                            <span class="badge badge-{{ $patient->patient_status === 'on_ward' ? 'primary' : ($patient->patient_status === 'transferred' ? 'warning' : 'success') }}"
                                  style="font-size: 13px;">
                                <i class="fas {{ $patient->patient_status === 'on_ward' ? 'fa-procedures' : ($patient->patient_status === 'transferred' ? 'fa-ambulance' : 'fa-home') }}"></i>
                                {{ $patient->status_label }}
                            </span>
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Ward</b>
                                <span class="float-right">{{ $patient->ward ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Daily Amount</b>
                                <span class="float-right">
                                    {{ $currency }} {{ number_format($patient->amount_to_pay, 0) }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Date of Admission</b>
                                <span class="float-right">{{ $patient->date_of_admission->format('Y-m-d') }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Days Since Admission</b>
                                <span class="float-right">{{ $daysAdmitted }} {{ Str::plural('day', $daysAdmitted) }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Total Due (Cumulative)</b>
                                <span class="float-right">
                                    {{ $currency }} {{ number_format($totalDue, 0) }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Balance Due</b>
                                <span class="float-right text-{{ $balance > 0 ? 'danger' : 'success' }} font-weight-bold">
                                    {{ $currency }} {{ number_format($balance, 0) }}
                                </span>
                            </li>
                            @if($patient->patient_status === 'discharged')
                                <li class="list-group-item">
                                    <b>Discharge Date</b>
                                    <span class="float-right">
                                        {{ $patient->date_of_discharge?->format('Y-m-d') ?? 'N/A' }}
                                    </span>
                                </li>
                            @endif
                            @if($patient->date_of_transfer)
                                <li class="list-group-item">
                                    <b>Transfer Date</b>
                                    <span class="float-right">{{ $patient->date_of_transfer->format('Y-m-d') }}</span>
                                </li>
                            @endif
                        </ul>

                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-info btn-block">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        <a href="{{ route('payments.create', ['patient_id' => $patient->id]) }}" class="btn btn-success btn-block mt-2">
                            <i class="fas fa-money-bill-wave"></i> Record Payment
                        </a>
                        <button type="button" class="btn btn-warning btn-block mt-2" data-toggle="modal" data-target="#changeStatusModal">
                            <i class="fas fa-exchange-alt"></i> Change Status
                        </button>
                    </div>
                </div>
            </div>

            {{-- ============== RIGHT MAIN ============== --}}
            <div class="col-md-8">

                {{-- Next of Kin --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users mr-1"></i> Next of Kin</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped mb-0">
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

                {{-- Discharge / Transfer notes (if any) --}}
                @if($patient->discharge_notes)
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">
                                <i class="fas fa-home mr-1"></i> Discharge Notes
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $patient->discharge_notes }}</p>
                        </div>
                    </div>
                @endif

                @if($patient->transfer_notes)
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title">
                                <i class="fas fa-ambulance mr-1"></i> Transfer Notes
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $patient->transfer_notes }}</p>
                        </div>
                    </div>
                @endif

                {{-- Assigned Caregivers --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-nurse mr-1"></i>
                            Assigned Caregivers ({{ $patient->caregivers->count() }})
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($patient->caregivers->count() > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->caregivers as $caregiver)
                                        <tr>
                                            <td>{{ $caregiver->id }}</td>
                                            <td>{{ $caregiver->name }}</td>
                                            <td>{{ $caregiver->phone }}</td>
                                            <td>
                                                <a href="{{ route('caregivers.show', $caregiver->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('patients.remove-caregiver', [$patient->id, $caregiver->id]) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Remove this caregiver?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No caregivers assigned.</p>
                        @endif

                        @if(isset($availableCaregivers) && $availableCaregivers->count() > 0)
                            <hr>
                            <h5>Assign New Caregiver</h5>
                            <form action="{{ route('patients.assign-caregiver', $patient->id) }}" method="POST" class="form-inline">
                                @csrf
                                <div class="form-group mr-2">
                                    <select name="caregiver_id" class="form-control" required>
                                        <option value="">Select Caregiver</option>
                                        @foreach($availableCaregivers as $caregiver)
                                            <option value="{{ $caregiver->id }}">
                                                {{ $caregiver->name }} - {{ $caregiver->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Assign
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Payment History --}}
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-1"></i>
                            Payment History ({{ $patient->payments->count() }})
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('payments.index', ['patient_id' => $patient->id]) }}"
                               class="btn btn-tool text-white" title="See all payments">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        @if($patient->payments->count() > 0)
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Date</th>
                                        <th>Payee</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">Balance</th>
                                        <th>Method</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->payments->take(10) as $payment)
                                        <tr>
                                            <td><small class="text-monospace">{{ $payment->receipt_number }}</small></td>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>{{ $payment->payee_name }}</td>
                                            <td class="text-right font-weight-bold">
                                                {{ $currency }} {{ number_format($payment->amount_paid, 0) }}
                                            </td>
                                            <td class="text-right">
                                                @if((float)$payment->running_balance > 0)
                                                    <span class="text-danger">
                                                        {{ $currency }} {{ number_format($payment->running_balance, 0) }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">Cleared</span>
                                                @endif
                                            </td>
                                            <td><small>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</small></td>
                                            <td>
                                                <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-info btn-xs">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="3" class="text-right">Totals:</th>
                                        <th class="text-right text-success">
                                            {{ $currency }} {{ number_format($totalPaid, 0) }}
                                        </th>
                                        <th class="text-right text-danger">
                                            {{ $currency }} {{ number_format($balance, 0) }}
                                        </th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                            @if($patient->payments->count() > 10)
                                <div class="card-footer text-center">
                                    <a href="{{ route('payments.index', ['patient_id' => $patient->id]) }}" class="text-info">
                                        View all {{ $patient->payments->count() }} payments &rarr;
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-muted text-center p-4 mb-0">
                                No payments recorded for this patient yet.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             Change Status Modal
             ============================================================ --}}
        <div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog"
             aria-labelledby="changeStatusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('patients.update-status', $patient->id) }}" id="status-change-form">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="changeStatusModalLabel">
                                <i class="fas fa-exchange-alt mr-1"></i> Change Patient Status
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-light border mb-3">
                                <small class="text-muted">
                                    Current status:
                                    <span class="badge badge-{{ $patient->patient_status === 'on_ward' ? 'primary' : ($patient->patient_status === 'transferred' ? 'warning' : 'success') }}">
                                        {{ $patient->status_label }}
                                    </span>
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="patient_status">New Status</label>
                                <select name="patient_status" class="form-control" required>
                                    <option value="on_ward"     {{ $patient->patient_status === 'on_ward'     ? 'selected' : '' }}>On Ward</option>
                                    <option value="transferred" {{ $patient->patient_status === 'transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="discharged"  {{ $patient->patient_status === 'discharged'  ? 'selected' : '' }}>Discharged</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="date_of_transfer">Transfer Date</label>
                                <input type="date" name="date_of_transfer" id="date_of_transfer"
                                       class="form-control"
                                       value="{{ old('date_of_transfer', $patient->date_of_transfer?->format('Y-m-d')) }}">
                            </div>
                            <div class="form-group">
                                <label for="transfer_notes">Transfer Notes</label>
                                <textarea name="transfer_notes" id="transfer_notes" class="form-control" rows="2"
                                          placeholder="Where transferred &amp; why">{{ old('transfer_notes', $patient->transfer_notes) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="date_of_discharge">Discharge Date</label>
                                <input type="date" name="date_of_discharge" id="date_of_discharge"
                                       class="form-control"
                                       value="{{ old('date_of_discharge', $patient->date_of_discharge?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                            </div>
                            <div class="form-group">
                                <label for="discharge_notes">Discharge Notes</label>
                                <textarea name="discharge_notes" id="discharge_notes" class="form-control" rows="2"
                                          placeholder="Reason / outcome of discharge">{{ old('discharge_notes', $patient->discharge_notes) }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Apply this status change to the patient?')">
                                <i class="fas fa-check mr-1"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop
