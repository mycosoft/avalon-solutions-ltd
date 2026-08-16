<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Caregiver;
use App\Models\Payment;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CaregiverPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|admin|accountant');
    }

    public function index(Request $request)
    {
        $payments = Payment::with('caregiver')
            ->where('payee_for', 'caregiver')
            ->when($request->caregiver_id, function ($q) use ($request) {
                $q->where('caregiver_id', $request->caregiver_id);
            })
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('payment_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('payment_date', '<=', $request->date_to);
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(15);

        $caregivers = Caregiver::orderBy('name')->get();

        return view('finance.caregiver-payments.index', compact('payments', 'caregivers'));
    }

    public function create(Request $request)
    {
        $caregiverId = $request->caregiver_id;
        $caregiver = $caregiverId ? Caregiver::findOrFail($caregiverId) : null;

        $caregivers = Caregiver::orderBy('name')->get();

        return view('finance.caregiver-payments.create', compact('caregivers', 'caregiver'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caregiver_id'    => 'required|exists:caregivers,id',
            'amount_paid'     => 'required|numeric|min:0',
            'payment_date'    => 'required|date',
            'period_start'    => 'required|date',
            'period_end'      => 'required|date|after_or_equal:period_start',
            'payment_method'  => 'required|in:cash,bank,mobile_money,other',
            'notes'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $caregiver = Caregiver::find($request->caregiver_id);

        $payment = Payment::create([
            'patient_id'     => null,
            'payee_for'      => 'caregiver',
            'caregiver_id'   => $request->caregiver_id,
            'payee_name'     => $caregiver->name,
            'amount_paid'    => $request->amount_paid,
            'daily_rate'     => 0,
            'monthly_rate'   => $caregiver->monthly_rate,
            'days_paid'      => 1,
            'payment_date'   => $request->payment_date,
            'period_start'   => $request->period_start,
            'period_end'     => $request->period_end,
            'payment_method' => $request->payment_method,
            'payment_type'   => 'full',
            'balance'        => 0,
            'notes'          => $request->notes,
            'recorded_by'    => Auth::user()->name,
        ]);

        UserNotification::notifyAccountants(
            'Caregiver Paid',
            "A payment of {$request->amount_paid} was made to caregiver {$caregiver->name} for period {$request->period_start} to {$request->period_end}.",
            'success'
        );

        return redirect()
            ->route('caregiver-payments.index')
            ->with('receipt_payment_id', $payment->id)
            ->with('success', 'Caregiver payment recorded successfully.');
    }

    public function show(Payment $caregiver_payment)
    {
        $caregiver_payment->load('caregiver');
        return view('finance.caregiver-payments.show', ['payment' => $caregiver_payment]);
    }

    public function receipt(Payment $caregiver_payment)
    {
        $caregiver_payment->load('caregiver');
        return view('finance.payments.receipt', [
            'payment'   => $caregiver_payment,
            'payeeType' => 'caregiver',
        ]);
    }

    public function getCaregiverRate($caregiverId)
    {
        $caregiver = Caregiver::findOrFail($caregiverId);

        // payment_plan = daily:  monthly_rate is per-day rate
        // payment_plan = monthly: monthly_rate is per-month rate
        $suggestedAmount = $caregiver->suggestedPayment(1);

        return response()->json([
            'caregiver'        => $caregiver,
            'name'             => $caregiver->name,
            'payment_plan'     => $caregiver->payment_plan,
            'rate'             => (float) $caregiver->monthly_rate,
            'daily_rate'       => (float) $caregiver->daily_rate,
            'suggested_amount' => $suggestedAmount,
            'label'            => $caregiver->payment_plan === 'monthly' ? 'monthly' : 'per day',
        ]);
    }
}
