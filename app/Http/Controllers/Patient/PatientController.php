<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientStatusLog;
use App\Models\Caregiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|admin');
    }

    public function index(Request $request)
    {
        $patients = Patient::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        })
        ->when($request->status, function ($query) use ($request) {
            $query->where('patient_status', $request->status);
        })
        ->when($request->ward, function ($query) use ($request) {
            $query->where('ward', $request->ward);
        })
        ->paginate(10);

        return view('patient.index', compact('patients'));
    }

    public function create()
    {
        $caregivers = Caregiver::where('status', true)->get();
        return view('patient.create', compact('caregivers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female',
            'relative_name' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:100',
            'amount_to_pay' => 'required|numeric|min:0',
            'date_of_admission' => 'required|date',
            'patient_status' => 'in:on_ward,transferred,discharged',
            'date_of_discharge' => 'nullable|date',
            'date_of_transfer' => 'nullable|date',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:50',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:500',
            'transfer_notes' => 'nullable|string',
            'discharge_notes' => 'nullable|string',
            'caregiver_ids' => 'nullable|array',
            'caregiver_ids.*' => 'exists:caregivers,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['caregiver_ids']);
        $data['patient_status'] = $request->patient_status ?? 'on_ward';

        $patient = Patient::create($data);

        // Seed the status log with the initial state so day-counting has a baseline.
        PatientStatusLog::create([
            'patient_id'     => $patient->id,
            'status'         => $patient->patient_status,
            'effective_date' => $patient->date_of_admission,
            'notes'          => 'Initial status on admission.',
        ]);

        if ($request->caregiver_ids) {
            $patient->caregivers()->attach($request->caregiver_ids, ['assignment_date' => now()]);
        }

        return redirect()->route('patients.index')->with('success', 'Patient added successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['caregivers', 'payments' => function ($query) {
            $query->where('payee_for', 'patient')->orWhereNull('payee_for')->orderBy('payment_date', 'desc');
        }]);
        $availableCaregivers = Caregiver::where('status', true)->whereDoesntHave('patients', function ($query) use ($patient) {
            $query->where('patient_id', $patient->id);
        })->get();

        return view('patient.show', compact('patient', 'availableCaregivers'));
    }

    /**
     * Quick status change for a patient (on_ward / transferred / discharged).
     * Updates the appropriate date + notes field automatically.
     */
    public function updateStatus(Request $request, Patient $patient)
    {
        $request->validate([
            'patient_status'   => 'required|in:on_ward,transferred,discharged',
            'date_of_discharge'=> 'nullable|date',
            'date_of_transfer' => 'nullable|date',
            'transfer_notes'   => 'nullable|string|max:2000',
            'discharge_notes'  => 'nullable|string|max:2000',
        ]);

        $data = ['patient_status' => $request->patient_status];

        if ($request->patient_status === 'discharged') {
            $data['date_of_discharge'] = $request->date_of_discharge ?: now()->format('Y-m-d');
            $data['discharge_notes']   = $request->discharge_notes;
        } elseif ($request->patient_status === 'transferred') {
            $data['date_of_transfer'] = $request->date_of_transfer ?: now()->format('Y-m-d');
            $data['transfer_notes']   = $request->transfer_notes;
        } else {
            // back on ward — clear transfer/discharge dates
            $data['date_of_discharge'] = null;
            $data['discharge_notes']   = null;
            $data['date_of_transfer']  = null;
            $data['transfer_notes']    = null;
        }

        $patient->update($data);

        // Record this transition in the patient's status log so that
        // getDaysAdmittedAttribute() can pause/resume/stop counting correctly.
        $effectiveDate = match ($request->patient_status) {
            'discharged'  => $data['date_of_discharge'] ?? now()->format('Y-m-d'),
            'transferred' => $data['date_of_transfer']  ?? now()->format('Y-m-d'),
            default       => now()->format('Y-m-d'), // on_ward resumes today
        };

        $logNotes = match ($request->patient_status) {
            'discharged'  => $request->discharge_notes,
            'transferred' => $request->transfer_notes,
            default       => $request->transfer_notes ?: $request->discharge_notes,
        };

        PatientStatusLog::create([
            'patient_id'     => $patient->id,
            'status'         => $request->patient_status,
            'effective_date' => $effectiveDate,
            'notes'          => $logNotes,
        ]);

        $label = [
            'on_ward'    => 'returned to ward',
            'transferred'=> 'transferred',
            'discharged' => 'discharged',
        ][$request->patient_status] ?? 'updated';

        return redirect()
            ->route('patients.show', $patient->id)
            ->with('success', "Patient status changed to {$label} successfully.");
    }

    public function edit(Patient $patient)
    {
        $caregivers = Caregiver::where('status', true)->get();
        $selectedCaregivers = $patient->caregivers->pluck('id')->toArray();
        return view('patient.edit', compact('patient', 'caregivers', 'selectedCaregivers'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female',
            'relative_name' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:100',
            'amount_to_pay' => 'required|numeric|min:0',
            'date_of_admission' => 'required|date',
            'patient_status' => 'in:on_ward,transferred,discharged',
            'date_of_discharge' => 'nullable|date',
            'date_of_transfer' => 'nullable|date',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:50',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:500',
            'transfer_notes' => 'nullable|string',
            'discharge_notes' => 'nullable|string',
            'caregiver_ids' => 'nullable|array',
            'caregiver_ids.*' => 'exists:caregivers,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['caregiver_ids']);

        if ($request->patient_status === 'discharged') {
            $data['date_of_discharge'] = $request->date_of_discharge ?? now();
        } elseif ($request->patient_status === 'transferred') {
            $data['date_of_transfer'] = $request->date_of_transfer ?? now();
        }

        $patient->update($data);

        if ($request->has('caregiver_ids')) {
            $patient->caregivers()->sync($request->caregiver_ids);
        }

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }

    public function assignCaregiver(Request $request, Patient $patient)
    {
        $request->validate([
            'caregiver_id' => 'required|exists:caregivers,id',
        ]);

        $patient->caregivers()->attach($request->caregiver_id, ['assignment_date' => now()]);

        return redirect()->back()->with('success', 'Caregiver assigned successfully.');
    }

    public function removeCaregiver(Patient $patient, Caregiver $caregiver)
    {
        $patient->caregivers()->detach($caregiver->id);
        return redirect()->back()->with('success', 'Caregiver removed from patient.');
    }
}
