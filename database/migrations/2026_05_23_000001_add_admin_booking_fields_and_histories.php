<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'internal_note')) {
                $table->text('internal_note')->nullable()->after('special_requests');
            }

            if (!Schema::hasColumn('bookings', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('internal_note');
            }

            if (!Schema::hasColumn('bookings', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('paid_at');
            }

            if (!Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('confirmed_at');
            }

            if (!Schema::hasColumn('bookings', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('cancelled_at');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'cancelled', 'completed', 'no_show') DEFAULT 'pending'");
        }

        Schema::create('booking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['table_id', 'date', 'time', 'status'], 'bookings_table_date_time_status_index');
            $table->index(['status', 'payment_status'], 'bookings_status_payment_status_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unique('reference_number', 'transactions_reference_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_histories');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_table_date_time_status_index');
            $table->dropIndex('bookings_status_payment_status_index');
            $table->dropColumn([
                'internal_note',
                'cancel_reason',
                'confirmed_at',
                'cancelled_at',
                'completed_at',
            ]);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('transactions_reference_number_unique');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending'");
        }
    }
};
