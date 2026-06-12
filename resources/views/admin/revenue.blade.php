<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doanh thu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        @include('admin.sidebar')

        <main class="flex-1 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Doanh thu</h1>
                    <p class="text-sm text-gray-500 mt-1">Đối soát giao dịch, doanh thu đã thanh toán và booking đã thanh toán.</p>
                </div>

                <a href="{{ route('admin.revenue.export', request()->query()) }}"
                   class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Xuất Excel
                </a>
            </div>

            <section class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Tiền thực nhận</p>
                    <p class="text-xl font-bold text-emerald-600">{{ number_format($stats['totalRevenue']) }}d</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Số giao dịch</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['transactionCount'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Booking đã thanh toán</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['paidBookings'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Doanh thu booking đã thanh toán</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['bookingRevenue']) }}d</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Giá trị TB/booking</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($stats['averageBookingValue']) }}d</p>
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <section class="bg-white rounded-lg shadow-sm p-6 xl:col-span-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Doanh thu theo ngày</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-3">Ngày</th>
                                    <th class="p-3">GD</th>
                                    <th class="p-3">Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenuesByDate as $item)
                                    <tr class="border-b">
                                        <td class="p-3">{{ \Carbon\Carbon::parse($item->revenue_date)->format('d/m/Y') }}</td>
                                        <td class="p-3">{{ $item->transaction_count }}</td>
                                        <td class="p-3 font-semibold">{{ number_format($item->total) }}d</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-gray-500">Không có dữ liệu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                <section class="bg-white rounded-lg shadow-sm p-6 xl:col-span-2">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Giao dịch gần đây</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-3">Số tham chiếu</th>
                                    <th class="p-3">Booking</th>
                                    <th class="p-3">Khách/Bàn</th>
                                    <th class="p-3">Tiền nhận</th>
                                    <th class="p-3">Dự kiến</th>
                                    <th class="p-3">Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    @php
                                        $booking = $transaction->booking;
                                        $expected = $booking->total_price ?? 0;
                                        $isMismatch = $booking && (float) $transaction->amount_in !== (float) $expected;
                                    @endphp
                                    <tr class="border-b align-top hover:bg-gray-50">
                                        <td class="p-3">
                                            <div class="font-semibold">{{ $transaction->reference_number }}</div>
                                            <div class="text-xs text-gray-500">{{ $transaction->gateway }}</div>
                                        </td>
                                        <td class="p-3">
                                            @if($booking)
                                                #{{ $booking->id }}
                                                <div class="text-xs text-gray-500">{{ $booking->payment_status }}</div>
                                            @else
                                                <span class="text-red-600">Khong map booking</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <div>{{ $booking->user->username ?? $booking->user->name ?? '' }}</div>
                                            <div class="text-xs text-gray-500">{{ $booking->table->name ?? '' }}</div>
                                        </td>
                                        <td class="p-3 font-semibold text-emerald-700">{{ number_format($transaction->amount_in) }}d</td>
                                        <td class="p-3">
                                            <span class="{{ $isMismatch ? 'text-red-600 font-bold' : '' }}">
                                                {{ $booking ? number_format($expected) . 'd' : '-' }}
                                            </span>
                                            @if($isMismatch)
                                                <div class="text-xs text-red-600">Lệch tiền</div>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            {{ optional($transaction->transaction_date)->format('d/m/Y H:i') }}
                                            <div class="text-xs text-gray-500 max-w-xs truncate">{{ $transaction->transaction_content }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-gray-500">Không có giao dịch.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>

</html>
