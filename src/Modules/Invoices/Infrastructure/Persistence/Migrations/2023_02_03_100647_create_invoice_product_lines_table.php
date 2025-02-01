<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_product_lines', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->string('product_name');
            $table->integer('unit_price');
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('invoice_product_lines');
    }
};
