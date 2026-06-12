<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booking_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('gateway');

            $table->timestamp('transaction_date')
                ->nullable();

            $table->string('account_number')
                ->nullable();

            $table->string('sub_account')
                ->nullable();

            $table->decimal('amount_in', 15, 2)
                ->default(0);

            $table->decimal('amount_out', 15, 2)
                ->default(0);

            $table->decimal('accumulated', 15, 2)
                ->default(0);

            $table->string('code')
                ->nullable();

            $table->text('transaction_content')
                ->nullable();

            $table->string('reference_number')
                ->nullable();

            $table->text('body')
                ->nullable();

            $table->json('raw_data')
                ->nullable();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
