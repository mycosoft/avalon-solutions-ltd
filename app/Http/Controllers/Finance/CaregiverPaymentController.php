<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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

    /**
     * Unpaid attendance days that count toward a caregiver's arrears.
     * Excludes inactive caregivers, days the caregiver was absent,
     * and days the patient was absent.
     */
    private function unpaidAttendances(int $caregiverId)
    {
        return Attendance::where('caregiver_id', $caregiverId)
            ->where('caregiver_present', true)
            ->where('is_paid', false)
            ->where('status', true)
            ->whereHas('caregiver', fn ($q) => $q->where('status', true))
            ->orderBy('date');
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
            ->paginate(15)->withQueryString();

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
            'payment_method'  => 'required|in:cash,bank,mobile_money,other',
            'notes'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $caregiver = Caregiver::find($request->caregiver_id);

        // Get unpaid attendance records for this caregiver
        $unpaidAttendances = $this->unpaidAttendances($request->caregiver_id)->get();

        $unpaidDays = $unpaidAttendances->count();
        $totalBalance = $unpaidDays * $caregiver->daily_rate;

        // Determine payment type and balance remaining
        $paymentType = 'partial';
        $balanceRemaining = $totalBalance - $request->amount_paid;

        if ($request->amount_paid >= $totalBalance) {
            $paymentType = 'full';
            $balanceRemaining = 0;
        }

        // Period covered by the paid attendances (falls back to the payment date)
        $periodStart = $unpaidAttendances->min('date') ?? $request->payment_date;
        $periodEnd   = $unpaidAttendances->max('date') ?? $request->payment_date;

        $payment = Payment::create([
            'patient_id'     => null,
            'payee_for'      => 'caregiver',
            'caregiver_id'   => $request->caregiver_id,
            'payee_name'     => $caregiver->name,
            'amount_paid'    => $request->amount_paid,
            'daily_rate'     => $caregiver->daily_rate,
            'monthly_rate'   => $caregiver->monthly_rate,
            'days_paid'      => $unpaidDays,
            'payment_date'   => $request->payment_date,
            'period_start'   => $periodStart,
            'period_end'     => $periodEnd,
            'payment_method' => $request->payment_method,
            'payment_type'   => $paymentType,
            'balance'        => max(0, $balanceRemaining),
            'notes'          => $request->notes,
            'recorded_by'    => Auth::user()->name,
        ]);

        // Mark attendance records as paid (up to the amount paid)
        $amountRemaining = $request->amount_paid;
        foreach ($unpaidAttendances as $attendance) {
            if ($amountRemaining <= 0) break;
            $attendance->update(['is_paid' => true]);
            $amountRemaining -= $caregiver->daily_rate;
        }

        UserNotification::notifyAccountants(
            'Caregiver Paid',
            "A payment of {$request->amount_paid} was made to caregiver {$caregiver->name}.",
            'success',
            route('payments.show', $payment->id)
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

        // Get unpaid days count and balance
        $unpaidDays = $this->unpaidAttendances($caregiverId)->count();

        $totalBalance = $unpaidDays * $caregiver->daily_rate;

        return response()->json([
            'caregiver'        => $caregiver,
            'name'            => $caregiver->name,
            'payment_plan'    => $caregiver->payment_plan,
            'rate'            => (float) $caregiver->monthly_rate,
            'daily_rate'      => (float) $caregiver->daily_rate,
            'unpaid_days'     => $unpaidDays,
            'total_balance'    => $totalBalance,
            'label'           => $caregiver->payment_plan === 'monthly' ? 'monthly' : 'per day',
        ]);
    }
}
