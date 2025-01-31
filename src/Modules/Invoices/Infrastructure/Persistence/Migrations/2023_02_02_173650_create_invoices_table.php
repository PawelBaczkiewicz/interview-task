<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Invoices\Domain\Enums\StatusEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->enum('status', array_map(fn($value) => $value->value, StatusEnum::cases()))
                ->default(StatusEnum::Draft->value);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
