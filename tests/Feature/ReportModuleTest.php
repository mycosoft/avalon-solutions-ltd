<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role_type' => 'superadmin',
        'status' => true,
    ]);

    $this->actingAs($this->user);
});

test('reports index page loads', function () {
    $this->get(route('reports.index'))
        ->assertStatus(200)
        ->assertSee('Available Reports');
});

test('each report type renders its report page', function (string $type) {
    $this->get(route('reports.show', $type))
        ->assertStatus(200);
})->with(['financial', 'payments', 'expenses', 'patients', 'caregivers', 'attendance']);

test('report pages respond to filters', function (string $type) {
    $this->get(route('reports.show', $type) . '?date_from=2026-01-01&date_to=2026-12-31')
        ->assertStatus(200);
})->with(['financial', 'payments', 'expenses', 'attendance']);

test('reports export as pdf', function (string $type) {
    $this->get(route('reports.export.pdf', $type))
        ->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');
})->with(['financial', 'payments', 'expenses', 'patients', 'caregivers', 'attendance']);

test('reports export as excel', function (string $type) {
    $this->get(route('reports.export.excel', $type))
        ->assertStatus(200)
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
})->with(['financial', 'payments', 'expenses', 'patients', 'caregivers', 'attendance']);

test('unknown report type returns 404', function () {
    $this->get(route('reports.show', 'unknown'))
        ->assertStatus(404);
});