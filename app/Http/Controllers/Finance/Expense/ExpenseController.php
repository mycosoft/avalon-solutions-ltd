<?php

namespace App\Http\Controllers\Finance\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|admin|accountant');
    }

    public function index(Request $request)
    {
        $expenses = Expense::when($request->category, function ($query) use ($request) {
            $query->where('category', $request->category);
        })
        ->when($request->date_from, function ($query) use ($request) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        })
        ->when($request->date_to, function ($query) use ($request) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        })
        ->orderBy('expense_date', 'desc')
        ->paginate(15);

        $categories = Expense::categories();

        return view('finance.expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $categories = Expense::categories();
        return view('finance.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|in:' . implode(',', array_keys(Expense::categories())),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $expense = Expense::create([
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'notes' => $request->notes,
            'recorded_by' => Auth::user()->name,
        ]);

        UserNotification::notifyAdmins(
            'New Expense Recorded',
            "A new expense of {$request->amount} was recorded for {$request->description}.",
            'info'
        );

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        return view('finance.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = Expense::categories();
        return view('finance.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|in:' . implode(',', array_keys(Expense::categories())),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function summary(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        $expensesByCategory = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return view('finance.expenses.summary', compact('totalExpenses', 'expensesByCategory', 'dateFrom', 'dateTo'));
    }
}
