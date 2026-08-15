<?php

namespace App\Http\Controllers\Caregiver;

use App\Http\Controllers\Controller;
use App\Models\Caregiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CaregiverController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|admin');
    }

    public function index(Request $request)
    {
        $caregivers = Caregiver::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('nin', 'like', '%' . $request->search . '%');
        })
        ->when($request->status !== null, function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        ->paginate(10);

        return view('caregiver.index', compact('caregivers'));
    }

    public function create()
    {
        return view('caregiver.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'nin' => 'required|string|max:20|unique:caregivers,nin',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'level_of_education' => 'nullable|string|max:100',
            'date_of_entry' => 'required|date',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:50',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('caregivers', 'public');
        }

        $data['status'] = $request->status ?? true;

        Caregiver::create($data);

        return redirect()->route('caregivers.index')->with('success', 'Caregiver added successfully.');
    }

    public function show(Caregiver $caregiver)
    {
        $caregiver->load('patients');
        return view('caregiver.show', compact('caregiver'));
    }

    public function edit(Caregiver $caregiver)
    {
        return view('caregiver.edit', compact('caregiver'));
    }

    public function update(Request $request, Caregiver $caregiver)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'nin' => 'required|string|max:20|unique:caregivers,nin,' . $caregiver->id,
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'level_of_education' => 'nullable|string|max:100',
            'date_of_entry' => 'required|date',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:50',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['photo']);

        if ($request->hasFile('photo')) {
            if ($caregiver->photo) {
                Storage::disk('public')->delete($caregiver->photo);
            }
            $data['photo'] = $request->file('photo')->store('caregivers', 'public');
        }

        $data['status'] = $request->status ?? true;

        $caregiver->update($data);

        return redirect()->route('caregivers.index')->with('success', 'Caregiver updated successfully.');
    }

    public function destroy(Caregiver $caregiver)
    {
        if ($caregiver->photo) {
            Storage::disk('public')->delete($caregiver->photo);
        }

        $caregiver->delete();

        return redirect()->route('caregivers.index')->with('success', 'Caregiver deleted successfully.');
    }
}
