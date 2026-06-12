<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Models\Booking;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveDateRange($request);

        $transactionQuery = Transaction::with('booking.user', 'booking.table')
            ->where('amount_in', '>', 0)
            ->whereBetween('transaction_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $transactions = (clone $transactionQuery)
            ->latest('transaction_date')
            ->paginate(10)
            ->withQueryString();

        $totalRevenue = (clone $transactionQuery)->sum('amount_in');
        $transactionCount = (clone $transactionQuery)->count();

        $paidBookingsQuery = Booking::where('payment_status', 'paid')
            ->whereBetween('paid_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $paidBookings = (clone $paidBookingsQuery)->count();
        $bookingRevenue = (clone $paidBookingsQuery)->sum('total_price');
        $averageBookingValue = $paidBookings > 0 ? $bookingRevenue / $paidBookings : 0;
        $unpaidBookings = Booking::where('payment_status', 'unpaid')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->count();

        $mismatchCount = Transaction::join('bookings', 'transactions.booking_id', '=', 'bookings.id')
            ->where('transactions.amount_in', '>', 0)
            ->whereBetween('transactions.transaction_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->whereColumn('transactions.amount_in', '!=', 'bookings.total_price')
            ->count();

        $revenuesByDate = (clone $transactionQuery)
            ->selectRaw('DATE(transaction_date) as revenue_date, SUM(amount_in) as total, COUNT(*) as transaction_count')
            ->groupBy('revenue_date')
            ->orderByDesc('revenue_date')
            ->get();

        $filters = [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
        ];

        $stats = compact(
            'totalRevenue',
            'transactionCount',
            'paidBookings',
            'bookingRevenue',
            'averageBookingValue',
            'unpaidBookings',
            'mismatchCount'
        );

        return view('admin.revenue', compact('transactions', 'revenuesByDate', 'filters', 'stats'));
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->resolveDateRange($request);

        $rows = [[
            'Mã giao dịch',
            'Mã booking',
            'Khách hàng',
            'Bàn',
            'Cổng thanh toán',
            'Số tiền',
            'Số tiền booking',
            'Ngày giao dịch',
            'Nội dung',
        ]];

        Transaction::with('booking.user', 'booking.table')
            ->where('amount_in', '>', 0)
            ->whereBetween('transaction_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->latest('transaction_date')
            ->get()
            ->each(function ($transaction) use (&$rows) {
                $booking = $transaction->booking;

                $rows[] = [
                    $transaction->reference_number,
                    $booking ? '#' . $booking->id : '',
                    $booking->user->username ?? $booking->user->name ?? '',
                    $booking->table->name ?? '',
                    $transaction->gateway,
                    $transaction->amount_in,
                    $booking->total_price ?? '',
                    optional($transaction->transaction_date)->format('d/m/Y H:i'),
                    $transaction->transaction_content,
                ];
            });

        return Excel::download(new ReportsExport($rows), 'revenue.xlsx');
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)
            : Carbon::now();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }
}
