<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Models\Booking;
use App\Models\Table;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveDateRange($request);

        $report = $this->buildReportData($from, $to);
        $filters = [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
        ];

        return view('admin.reports', array_merge($report, compact('filters')));
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->resolveDateRange($request);
        $report = $this->buildReportData($from, $to);

        $rows = [
            ['Thời gian báo cáo', $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y')],
            [],
            ['Tổng quan', 'Giá trị'],
            ['Tổng số booking', $report['bookingStats']['total']],
            ['Tổng tiền đã thanh toán', $report['paymentStats']['paid_revenue']],
            ['Doanh thu giao dịch', $report['paymentStats']['transaction_revenue']],
            ['Tổng người dùng', $report['userStats']['total_users']],
            ['Người dùng mới trong kỳ', $report['userStats']['new_users']],
            [],
            ['Trạng thái booking', 'Số lượng'],
        ];

        foreach ($report['bookingStatusBreakdown'] as $status => $count) {
            $rows[] = [$status, $count];
        }

        $rows[] = [];
        $rows[] = ['Trạng thái thanh toán', 'Số lượng'];
        foreach ($report['paymentStatusBreakdown'] as $status => $count) {
            $rows[] = [$status, $count];
        }

        $rows[] = [];
        $rows[] = ['Địa điểm', 'Số booking', 'Doanh thu'];
        foreach ($report['locationBreakdown'] as $item) {
            $rows[] = [$item->location ?: 'N/A', $item->booking_count, $item->revenue];
        }

        $rows[] = [];
        $rows[] = ['Bàn được đặt nhiều nhất', 'Số lượng booking', 'Doanh thu'];
        foreach ($report['topTables'] as $item) {
            $rows[] = [$item->table_name, $item->booking_count, $item->revenue];
        }

        return Excel::download(new ReportsExport($rows), 'bao_cao.xlsx');
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

    private function buildReportData(Carbon $from, Carbon $to): array
    {
        $fromDate = $from->copy()->startOfDay();
        $toDate = $to->copy()->endOfDay();

        $bookingQuery = Booking::whereBetween('created_at', [$fromDate, $toDate]);
        $bookingStatusBreakdown = [
            'pending' => (clone $bookingQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $bookingQuery)->where('status', 'confirmed')->count(),
            'cancelled' => (clone $bookingQuery)->where('status', 'cancelled')->count(),
            'completed' => (clone $bookingQuery)->where('status', 'completed')->count(),
            'no_show' => (clone $bookingQuery)->where('status', 'no_show')->count(),
        ];

        $bookingStats = [
            'total' => (clone $bookingQuery)->count(),
            'guests' => (clone $bookingQuery)->sum('guest_count'),
        ];

        $paidBookingQuery = Booking::where('payment_status', 'paid')
            ->whereBetween('paid_at', [$fromDate, $toDate]);

        $paymentStatusBreakdown = [
            'paid' => (clone $paidBookingQuery)->count(),
            'unpaid' => Booking::where('payment_status', 'unpaid')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->count(),
            'failed' => Booking::where('payment_status', 'failed')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->count(),
        ];

        $transactionRevenue = Transaction::where('amount_in', '>', 0)
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->sum('amount_in');

        $paymentStats = [
            'paid_revenue' => (clone $paidBookingQuery)->sum('total_price'),
            'transaction_revenue' => $transactionRevenue,
            'average_paid_booking' => (clone $paidBookingQuery)->count() > 0
                ? (clone $paidBookingQuery)->sum('total_price') / (clone $paidBookingQuery)->count()
                : 0,
        ];

        $tableStats = [
            'total' => Table::count(),
            'available' => Table::where('status', 'available')->count(),
            'reserved' => Table::where('status', 'reserved')->count(),
            'occupied' => Table::where('status', 'occupied')->count(),
        ];

        $locationBreakdown = Booking::join('tables', 'bookings.table_id', '=', 'tables.id')
            ->selectRaw("tables.location, COUNT(bookings.id) as booking_count, SUM(CASE WHEN bookings.payment_status = 'paid' THEN bookings.total_price ELSE 0 END) as revenue")
            ->whereBetween('bookings.created_at', [$fromDate, $toDate])
            ->groupBy('tables.location')
            ->orderByDesc('booking_count')
            ->get();

        $topTables = Booking::join('tables', 'bookings.table_id', '=', 'tables.id')
            ->selectRaw("tables.name as table_name, tables.location, COUNT(bookings.id) as booking_count, SUM(CASE WHEN bookings.payment_status = 'paid' THEN bookings.total_price ELSE 0 END) as revenue")
            ->whereBetween('bookings.created_at', [$fromDate, $toDate])
            ->groupBy('tables.id', 'tables.name', 'tables.location')
            ->orderByDesc('booking_count')
            ->take(5)
            ->get();

        $userStats = [
            'total_users' => User::count(),
            'new_users' => User::whereBetween('created_at', [$fromDate, $toDate])->count(),
            'users_with_bookings' => Booking::whereBetween('created_at', [$fromDate, $toDate])
                ->distinct('user_id')
                ->count('user_id'),
        ];

        $topCustomers = Booking::join('users', 'bookings.user_id', '=', 'users.id')
            ->selectRaw("users.username, users.email, COUNT(bookings.id) as booking_count, SUM(CASE WHEN bookings.payment_status = 'paid' THEN bookings.total_price ELSE 0 END) as spent")
            ->whereBetween('bookings.created_at', [$fromDate, $toDate])
            ->groupBy('users.id', 'users.username', 'users.email')
            ->orderByDesc('spent')
            ->take(5)
            ->get();

        return compact(
            'bookingStats',
            'bookingStatusBreakdown',
            'paymentStats',
            'paymentStatusBreakdown',
            'tableStats',
            'locationBreakdown',
            'topTables',
            'userStats',
            'topCustomers'
        );
    }
}
