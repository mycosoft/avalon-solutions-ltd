<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->timestamps();
        });

        // Seed default settings
        $defaults = [
            ['key' => 'company_name',       'value' => 'Avalon Solutions',          'group' => 'company'],
            ['key' => 'company_address',    'value' => '',                          'group' => 'company'],
            ['key' => 'company_phone',      'value' => '',                          'group' => 'company'],
            ['key' => 'company_email',      'value' => '',                          'group' => 'company'],
            ['key' => 'company_tagline',    'value' => 'Care, Compassion, Commitment', 'group' => 'company'],
            ['key' => 'currency_symbol',    'value' => 'UGX',                       'group' => 'finance'],
            ['key' => 'currency_code',      'value' => 'UGX',                       'group' => 'finance'],
            ['key' => 'default_payment_method', 'value' => 'cash',                   'group' => 'finance'],
            ['key' => 'receipt_footer',     'value' => 'Thank you for your payment!', 'group' => 'receipt'],
            ['key' => 'receipt_show_logo',  'value' => '1',                         'group' => 'receipt'],
            ['key' => 'low_balance_threshold', 'value' => '50000',                   'group' => 'finance'],
        ];

        foreach ($defaults as $row) {
            \DB::table('settings')->insert($row + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
