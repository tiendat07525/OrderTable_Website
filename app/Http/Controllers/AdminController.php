<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Table;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $now = now();

        $todayBookings = Booking::whereDate('date', $today)->count();
        $todayPending = Booking::whereDate('date', $today)->where('status', 'pending')->count();
        $todayConfirmed = Booking::whereDate('date', $today)->where('status', 'confirmed')->count();
        $todayCancelled = Booking::whereDate('date', $today)->where('status', 'cancelled')->count();
        $todayCompleted = Booking::whereDate('date', $today)->where('status', 'completed')->count();

        $totalTables = Table::count();

        $bookedTables = Booking::whereDate('date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->distinct('table_id')
            ->count('table_id');

        $availableTables = max($totalTables - $bookedTables, 0);

        $totalUsers = User::count();

        $todayPaidRevenue = Transaction::where('amount_in', '>', 0)
            ->whereDate('transaction_date', $today)
            ->sum('amount_in');

        $monthlyRevenue = Transaction::where('amount_in', '>', 0)
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount_in') / 1000000;

        $todayPaidBookings = Booking::whereDate('paid_at', $today)
            ->where('payment_status', 'paid')
            ->count();

        $todayUnpaidBookings = Booking::whereDate('date', $today)
            ->where('payment_status', 'unpaid')
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $upcomingSoonBookings = Booking::with(['user', 'table'])
            ->whereDate('date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereTime('time', '>=', $now->format('H:i:s'))
            ->whereTime('time', '<=', $now->copy()->addHour()->format('H:i:s'))
            ->orderBy('time')
            ->get();

        $paymentMismatchCount = Transaction::join('bookings', 'transactions.booking_id', '=', 'bookings.id')
            ->where('transactions.amount_in', '>', 0)
            ->whereColumn('transactions.amount_in', '!=', 'bookings.total_price')
            ->count();

        $recentBookings = Booking::with(['user', 'table'])
            ->latest()
            ->take(8)
            ->get();

        $todayTimeline = Booking::with(['user', 'table'])
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        $labels = [];
        $revenues = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $labels[] = $date->format('d/m');
            $revenues[] = Transaction::where('amount_in', '>', 0)
                ->whereDate('transaction_date', $date)
                ->sum('amount_in') / 1000000;
        }

        $statusCounts = [
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'no_show' => Booking::where('status', 'no_show')->count(),
        ];

        $paymentCounts = [
            'paid' => Booking::where('payment_status', 'paid')->count(),
            'unpaid' => Booking::where('payment_status', 'unpaid')->count(),
            'failed' => Booking::where('payment_status', 'failed')->count(),
        ];

        return view('admin.dashboard', compact(
            'todayBookings',
            'todayPending',
            'todayConfirmed',
            'todayCancelled',
            'todayCompleted',
            'availableTables',
            'totalTables',
            'monthlyRevenue',
            'todayPaidRevenue',
            'todayPaidBookings',
            'todayUnpaidBookings',
            'totalUsers',
            'upcomingSoonBookings',
            'paymentMismatchCount',
            'recentBookings',
            'todayTimeline',
            'labels',
            'revenues',
            'statusCounts',
            'paymentCounts'
        ));
    }
}
