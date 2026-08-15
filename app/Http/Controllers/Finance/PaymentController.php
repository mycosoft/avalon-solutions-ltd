<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Patient;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|admin|accountant');
    }

    public function index(Request $request)
    {
        $payments = Payment::with('patient')
            ->when($request->patient_id, function ($query) use ($request) {
                $query->where('patient_id', $request->patient_id);
            })
            ->when($request->date_from, function ($query) use ($request) {
                $query->whereDate('payment_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->whereDate('payment_date', '<=', $request->date_to);
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(15);

        $patients = Patient::where('is_active', true)->get();

        return view('finance.payments.index', compact('payments', 'patients'));
    }

    public function create(Request $request)
    {
        $patientId = $request->patient_id;
        $patient = null;
        $balance = 0;
        $totalDays = 0;
        $totalAmount = 0;
        $paidAmount = 0;

        if ($patientId) {
            $patient = Patient::findOrFail($patientId);
            $totalDays = now()->diffInDays($patient->date_of_admission);
            $totalAmount = $totalDays * $patient->amount_to_pay;
            $paidAmount = Payment::where('patient_id', $patientId)->sum('amount_paid');
            $balance = max(0, $totalAmount - $paidAmount);
        }

        $patients = Patient::where('is_active', true)->get();

        return view('finance.payments.create', compact('patients', 'patient', 'balance', 'totalDays', 'totalAmount', 'paidAmount'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'payee_name' => 'required|string|max:255',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'days_paid' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,bank,mobile_money,other',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $patient = Patient::find($request->patient_id);
        $dailyRate = $patient->amount_to_pay;
        $periodStart = $request->payment_date;
        $periodEnd = now()->addDays($request->days_paid - 1)->format('Y-m-d');

        $totalDays = now()->diffInDays($patient->date_of_admission);
        $totalAmount = $totalDays * $dailyRate;
        $paidAmount = Payment::where('patient_id', $request->patient_id)->sum('amount_paid');
        $previousBalance = max(0, $totalAmount - $paidAmount);

        $newBalance = max(0, $previousBalance - $request->amount_paid);
        $paymentType = $newBalance > 0 ? 'partial' : 'full';

        $payment = Payment::create([
            'patient_id' => $request->patient_id,
            'payee_name' => $request->payee_name,
            'amount_paid' => $request->amount_paid,
            'daily_rate' => $dailyRate,
            'days_paid' => $request->days_paid,
            'payment_date' => $request->payment_date,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'payment_method' => $request->payment_method,
            'payment_type' => $paymentType,
            'balance' => $newBalance,
            'notes' => $request->notes,
            'recorded_by' => Auth::user()->name,
        ]);

        if ($newBalance > 0) {
            UserNotification::notifyAccountants(
                'Partial Payment Received',
                "A partial payment of {$request->amount_paid} was received from {$request->payee_name} for patient {$patient->name}. Balance: {$newBalance}",
                'warning'
            );
        } else {
            UserNotification::notifyAccountants(
                'Payment Received',
                "A payment of {$request->amount_paid} was received from {$request->payee_name} for patient {$patient->name}.",
                'success'
            );
        }

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('patient');
        return view('finance.payments.show', compact('payment'));
    }

    public function getPatientBalance($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $totalDays = now()->diffInDays($patient->date_of_admission);
        $totalAmount = $totalDays * $patient->amount_to_pay;
        $paidAmount = Payment::where('patient_id', $patientId)->sum('amount_paid');
        $balance = max(0, $totalAmount - $paidAmount);

        return response()->json([
            'patient' => $patient,
            'daily_rate' => $patient->amount_to_pay,
            'total_days' => $totalDays,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance' => $balance,
        ]);
    }
}
