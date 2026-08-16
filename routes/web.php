<?php

use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Caregiver\CaregiverController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Finance\Expense\ExpenseController;
use App\Http\Controllers\Finance\CaregiverPaymentController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Settings\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::middleware(['auth', 'checkUserStatus'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware(['role:superadmin|admin'])->group(function () {
        Route::get('/caregivers', [CaregiverController::class, 'index'])->name('caregivers.index');
        Route::get('/caregivers/create', [CaregiverController::class, 'create'])->name('caregivers.create');
        Route::post('/caregivers', [CaregiverController::class, 'store'])->name('caregivers.store');
        Route::get('/caregivers/{caregiver}', [CaregiverController::class, 'show'])->name('caregivers.show');
        Route::get('/caregivers/{caregiver}/edit', [CaregiverController::class, 'edit'])->name('caregivers.edit');
        Route::put('/caregivers/{caregiver}', [CaregiverController::class, 'update'])->name('caregivers.update');
        Route::delete('/caregivers/{caregiver}', [CaregiverController::class, 'destroy'])->name('caregivers.destroy');

        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
        Route::post('/patients/{patient}/assign-caregiver', [PatientController::class, 'assignCaregiver'])->name('patients.assign-caregiver');
        Route::post('/patients/{patient}/remove-caregiver/{caregiver}', [PatientController::class, 'removeCaregiver'])->name('patients.remove-caregiver');
        Route::patch('/patients/{patient}/status', [PatientController::class, 'updateStatus'])->name('patients.update-status');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::get('/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware(['role:superadmin|admin|accountant'])->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/patient-balance/{patientId}', [PaymentController::class, 'getPatientBalance'])->name('payments.patient-balance');

        Route::get('/caregiver-payments', [CaregiverPaymentController::class, 'index'])->name('caregiver-payments.index');
        Route::get('/caregiver-payments/create', [CaregiverPaymentController::class, 'create'])->name('caregiver-payments.create');
        Route::post('/caregiver-payments', [CaregiverPaymentController::class, 'store'])->name('caregiver-payments.store');
        Route::get('/caregiver-payments/{caregiver_payment}/receipt', [CaregiverPaymentController::class, 'receipt'])->name('caregiver-payments.receipt');
        Route::get('/caregiver-payments/{caregiver_payment}', [CaregiverPaymentController::class, 'show'])->name('caregiver-payments.show');
        Route::get('/caregiver-payments/caregiver-rate/{caregiverId}', [CaregiverPaymentController::class, 'getCaregiverRate'])->name('caregiver-payments.caregiver-rate');

        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
        Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{type}/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/{type}/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/reports/{type}', [ReportController::class, 'show'])->name('reports.show');
    });

    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/roles', [RolePermissionController::class, 'rolesIndex'])->name('roles.index');
        Route::post('/roles', [RolePermissionController::class, 'rolesStore'])->name('roles.store');
        Route::put('/roles/{role}', [RolePermissionController::class, 'rolesUpdate'])->name('roles.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'rolesDestroy'])->name('roles.destroy');

        Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])->name('permissions.index');
        Route::post('/permissions', [RolePermissionController::class, 'permissionsStore'])->name('permissions.store');
        Route::put('/permissions/{permission}', [RolePermissionController::class, 'permissionsUpdate'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [RolePermissionController::class, 'permissionsDestroy'])->name('permissions.destroy');
    });

    // Settings - superadmin & admin
    Route::middleware(['role:superadmin|admin'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    Route::get('/notifications/unread', [NotificationController::class, 'fetchUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});
