<?php

use App\Models\Invoice;
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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invoice::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('pax_name');
            $table->text('service_description');
            $table->double('unit_price');
            $table->double('taxable_amount')->default(0.00);
            $table->double('tax_rate')->default(0.00);
            $table->double('tax_amount')->default(0.00);
            $table->double('total')->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
