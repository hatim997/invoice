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
        Schema::create('invoice_agency_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invoice::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->text('category_name_english');
            $table->text('category_name_arabic');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_agency_categories');
    }
};
