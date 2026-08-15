<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\UserNotification;
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

    public function create()
    {
        $caregivers = Caregiver::where('status', true)->get();
        $patients = Patient::where('is_active', true)->get();
        return view('attendance.create', compact('caregivers', 'patients'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caregiver_id' => 'required|exists:caregivers,id',
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'ward' => 'nullable|string|max:100',
            'days_under_care' => 'nullable|integer|min:0',
            'admin_observation' => 'nullable|string',
            'complaint_reported' => 'nullable|string',
            'complaint_assignment' => 'nullable|string',
            'follow_up' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $patient = Patient::find($request->patient_id);
        $daysUnderCare = now()->diffInDays($patient->date_of_admission);

        $data = $request->all();
        $data['days_under_care'] = $daysUnderCare;

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
            'caregiver_id' => 'required|exists:caregivers,id',
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'ward' => 'nullable|string|max:100',
            'days_under_care' => 'nullable|integer|min:0',
            'admin_observation' => 'nullable|string',
            'complaint_reported' => 'nullable|string',
            'complaint_assignment' => 'nullable|string',
            'follow_up' => 'nullable|string',
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
