@extends('adminlte::page')

@section('title', 'New Attendance')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daily Ward Round</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item active">Ward Round</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-procedures mr-2"></i>Daily Patient Checkup Form</h3>
                    </div>
                    <form method="POST" action="{{ route('attendance.store') }}" id="attendance-form">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date">Date *</label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                        @error('date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="patient_id">Patient * <small class="text-muted">(Select a patient &mdash; their assigned caregivers will be highlighted)</small></label>
                                        <select class="form-control @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                            <option value="">Select Patient</option>
                                            @foreach($patients as $pt)
                                                <option value="{{ $pt->id }}" data-ward="{{ $pt->ward }}" data-caregivers="{{ $pt->caregivers->pluck('id')->implode(',') }}" {{ old('patient_id') == $pt->id ? 'selected' : '' }}>
                                                    {{ $pt->name }} &mdash; {{ $pt->ward ?? 'No Ward' }}
                                                    @if($pt->caregivers->isNotEmpty())
                                                        ({{ $pt->caregivers->pluck('name')->implode(', ') }})
                                                    @else
                                                        (Unassigned)
                                                    @endif
                                                </option>
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
                                        <input type="text" class="form-control" id="ward" name="ward" value="{{ old('ward') }}" placeholder="Auto-filled from patient">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="caregiver_id">
                                            Caregiver
                                            <small class="text-muted">(auto-suggested from patient&rsquo;s assignment)</small>
                                        </label>
                                        <select class="form-control @error('caregiver_id') is-invalid @enderror" id="caregiver_id" name="caregiver_id">
                                            <option value="">&mdash; Unassigned / Not specified &mdash;</option>
                                            @foreach($caregivers as $cg)
                                                <option value="{{ $cg->id }}" {{ old('caregiver_id') == $cg->id ? 'selected' : '' }}>{{ $cg->name }}</option>
                                            @endforeach
                                        </select>
                                        <small id="caregiver-hint" class="text-muted"></small>
                                        @error('caregiver_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="days_under_care">Days Under Care</label>
                                        <input type="number" class="form-control" id="days_under_care" name="days_under_care" value="{{ old('days_under_care', 0) }}" min="0" readonly>
                                        <small class="text-muted"><i>Auto-calculated</i></small>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="text-info"><i class="fas fa-clipboard-list mr-1"></i> Observations &amp; Reports</h5>

                            <div class="form-group">
                                <label for="admin_observation">Admin Observation</label>
                                <textarea class="form-control" id="admin_observation" name="admin_observation" rows="3" placeholder="Enter general observations...">{{ old('admin_observation') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="complaint_reported">Complaint or Issues Reported</label>
                                <textarea class="form-control" id="complaint_reported" name="complaint_reported" rows="3" placeholder="Enter any complaints or issues reported...">{{ old('complaint_reported') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="complaint_assignment">Complaint Assignment</label>
                                <textarea class="form-control" id="complaint_assignment" name="complaint_assignment" rows="3" placeholder="Who is assigned to handle the complaint...">{{ old('complaint_assignment') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="follow_up">Follow Up</label>
                                <textarea class="form-control" id="follow_up" name="follow_up" rows="3" placeholder="Any follow-up actions required...">{{ old('follow_up') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save mr-1"></i> Save Attendance
                            </button>
                            <a href="{{ route('attendance.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    (function () {
        var patientSelect = document.getElementById('patient_id');
        var caregiverSelect = document.getElementById('caregiver_id');
        var wardInput = document.getElementById('ward');
        var caregiverHint = document.getElementById('caregiver-hint');

        function syncFromPatient() {
            var opt = patientSelect.options[patientSelect.selectedIndex];
            if (!opt || !opt.value) {
                caregiverHint.textContent = '';
                return;
            }

            // 1) Auto-fill ward
            var ward = opt.getAttribute('data-ward');
            if (ward && !wardInput.value) {
                wardInput.value = ward;
            }

            // 2) Highlight / auto-select the patient's assigned caregivers
            var assignedIds = (opt.getAttribute('data-caregivers') || '')
                .split(',')
                .map(function (s) { return s.trim(); })
                .filter(Boolean);

            // Visually mark assigned caregivers in the dropdown
            for (var i = 0; i < caregiverSelect.options.length; i++) {
                var cOpt = caregiverSelect.options[i];
                var originalLabel = cOpt.getAttribute('data-original') || cOpt.textContent;
                if (!cOpt.getAttribute('data-original')) {
                    cOpt.setAttribute('data-original', originalLabel);
                }
                if (cOpt.value && assignedIds.indexOf(cOpt.value) !== -1) {
                    cOpt.textContent = '\u2713 ' + originalLabel + '  (assigned)';
                } else {
                    cOpt.textContent = originalLabel;
                }
            }

            // 3) Auto-pick the first assigned caregiver (if any) and only if user
            //    hasn't picked one yet (preserves user choice on form re-render).
            if (!caregiverSelect.value || caregiverSelect.value === '') {
                if (assignedIds.length > 0) {
                    caregiverSelect.value = assignedIds[0];
                }
            }

            // 4) Update hint
            if (assignedIds.length === 0) {
                caregiverHint.innerHTML = '<i class="fas fa-info-circle text-warning"></i> No caregiver assigned to this patient yet.';
            } else {
                caregiverHint.innerHTML = '<i class="fas fa-check-circle text-success"></i> ' + assignedIds.length + ' caregiver(s) assigned to this patient.';
            }
        }

        if (patientSelect) {
            patientSelect.addEventListener('change', syncFromPatient);
            // Run once for the initial value (handles old() round-trip on validation errors)
            syncFromPatient();
        }
    })();
</script>
@stop