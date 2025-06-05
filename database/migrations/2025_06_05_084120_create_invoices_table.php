<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('agency_name');
            $table->string('agency_licence_number');

            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->string('invoice_hijri_date')->nullable();
            $table->string('vat_reg_no');

            $table->double('vat')->default(0.00);
            $table->string('stamp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
