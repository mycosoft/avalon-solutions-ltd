<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|admin');
    }

    public function index(Request $request)
    {
        $attendances = Attendance::with(['caregiver', 'patient'])
            ->when($request->date, function ($query) use ($request) {
                $query->whereDate('date', $request->date);
            })
            ->when($request->caregiver_id, function ($query) use ($request) {
                $query->where('caregiver_id', $request->caregiver_id);
            })
            ->when($request->patient_id, function ($query) use ($request) {
                $query->where('patient_id', $request->patient_id);
            })
            ->orderBy('date', 'desc')
            ->paginate(15);

        $caregivers = Caregiver::where('status', true)->get();
        $patients = Patient::where('is_active', true)->get();

        return view('attendance.index', compact('attendances', 'caregivers', 'patients'));
    }

    public function create(Request $request)
    {
        $caregivers = Caregiver::where('status', true)->get();
        // Eager-load each patient's currently assigned caregivers so the form can
        // auto-fill the caregiver dropdown when a patient is picked.
        $patients = Patient::with(['caregivers' => function ($q) {
            $q->orderBy('name');
        }])->where('is_active', true)->orderBy('ward')->orderBy('name')->get();

        return view('attendance.create', compact('caregivers', 'patients'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caregiver_id' => 'nullable|exists:caregivers,id',
            'patient_id'   => 'required|exists:patients,id',
            'date'         => 'required|date',
            'ward'         => 'nullable|string|max:100',
            'days_under_care' => 'nullable|integer|min:0',
            'admin_observation'    => 'nullable|string',
            'complaint_reported'   => 'nullable|string',
            'complaint_assignment' => 'nullable|string',
            'follow_up'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $patient = Patient::with('caregivers')->find($request->patient_id);
        // Days under care is computed against the ATTENDANCE date (not today)
        // so back-filled records report the correct count.
        $attendanceDate = Carbon::parse($request->date);
        $daysUnderCare = ($patient->date_of_admission && $patient->date_of_admission->lte($attendanceDate))
            ? (int) $patient->date_of_admission->diffInDays($attendanceDate)
            : 0;

        // Auto-fill caregiver from the patient's first assigned caregiver if none selected.
        $caregiverId = $request->caregiver_id;
        if (! $caregiverId && $patient->caregivers->isNotEmpty()) {
            $caregiverId = $patient->caregivers->first()->id;
        }

        $data = $request->except(['caregiver_id']);
        $data['days_under_care'] = $daysUnderCare;
        $data['caregiver_id'] = $caregiverId;

        $attendance = Attendance::create($data);

        if ($request->complaint_reported) {
            UserNotification::notifyAdmins(
                'Complaint Reported',
                "A complaint was reported for patient {$patient->name} by caregiver.",
                'alert'
            );
        }

        return redirect()->route('attendance.index')->with('success', 'Attendance record added successfully.');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['caregiver', 'patient']);
        return view('attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $caregivers = Caregiver::where('status', true)->get();
        $patients = Patient::where('is_active', true)->get();
        return view('attendance.edit', compact('attendance', 'caregivers', 'patients'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validator = Validator::make($request->all(), [
            'caregiver_id' => 'nullable|exists:caregivers,id',
            'patient_id'   => 'required|exists:patients,id',
            'date'         => 'required|date',
            'ward'         => 'nullable|string|max:100',
            'days_under_care' => 'nullable|integer|min:0',
            'admin_observation'    => 'nullable|string',
            'complaint_reported'   => 'nullable|string',
            'complaint_assignment' => 'nullable|string',
            'follow_up'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $attendance->update($request->all());

        return redirect()->route('attendance.index')->with('success', 'Attendance record updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance record deleted successfully.');
    }
}
