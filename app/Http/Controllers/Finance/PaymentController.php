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
        $totalDue = 0;
        $paidAmount = 0;

        if ($patientId) {
            $patient = Patient::findOrFail($patientId);
            $totalDays = $patient->days_admitted;
            $totalDue  = $patient->total_due;
            $paidAmount = $patient->total_paid;
            $balance   = $patient->balance;
        }

        $patients = Patient::where('is_active', true)->get();

        return view('finance.payments.create', compact('patients', 'patient', 'balance', 'totalDays', 'totalDue', 'paidAmount'));
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

        // Compute the new running balance as:
        //   (current total due at this moment) - (cumulative payments up to & incl. this one)
        // The cumulative includes the amount we are about to record.
        $totalDue   = $patient->total_due;
        $paidBefore = (float) Payment::where('patient_id', $patient->id)
            ->where(function ($q) {
                $q->where('payee_for', 'patient')->orWhereNull('payee_for');
            })
            ->where(function ($q) use ($request) {
                $q->where('payment_date', '<', $request->payment_date)
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('payment_date', '=', $request->payment_date);
                  });
            })
            ->sum('amount_paid');

        $newBalance = max(0, round($totalDue - ($paidBefore + $request->amount_paid), 2));
        $paymentType = $newBalance > 0 ? 'partial' : 'full';

        $payment = Payment::create([
            'payee_for'      => 'patient',
            'patient_id'     => $request->patient_id,
            'payee_name'     => $request->payee_name,
            'amount_paid'    => $request->amount_paid,
            'daily_rate'     => $dailyRate,
            'monthly_rate'   => null,
            'days_paid'      => $request->days_paid,
            'payment_date'   => $request->payment_date,
            'period_start'   => $periodStart,
            'period_end'     => $periodEnd,
            'payment_method' => $request->payment_method,
            'payment_type'   => $paymentType,
            'balance'        => $newBalance,
            'notes'          => $request->notes,
            'recorded_by'    => Auth::user()->name,
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

        return redirect()
            ->route('payments.index')
            ->with('receipt_payment_id', $payment->id)
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('patient');
        return view('finance.payments.show', compact('payment'));
    }

    public function receipt(Payment $payment)
    {
        $payment->load('patient');
        return view('finance.payments.receipt', [
            'payment'   => $payment,
            'payeeType' => 'patient',
        ]);
    }

    public function getPatientBalance($patientId)
    {
        $patient = Patient::findOrFail($patientId);

        return response()->json([
            'patient'     => $patient,
            'daily_rate'  => (float) $patient->amount_to_pay,
            'total_days'  => $patient->days_admitted,
            'total_due'   => $patient->total_due,
            'paid_amount' => $patient->total_paid,
            'balance'     => $patient->balance,
            'days_owed'   => $patient->days_owed,
            'admission'   => $patient->date_of_admission?->format('Y-m-d'),
        ]);
    }
}
