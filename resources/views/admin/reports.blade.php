<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        @include('admin.sidebar')

        <main class="flex-1 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Báo cáo</h1>
                    <p class="text-sm text-gray-500 mt-1">Tổng hợp booking, payment, bàn và user theo khoảng ngày.</p>
                </div>

                <a href="{{ route('admin.reports.export', request()->query()) }}"
                   class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Xuất Excel
                </a>
            </div>

            <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Tổng booking</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $bookingStats['total'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Doanh thu đã thanh toán</p>
                    <p class="text-xl font-bold text-emerald-600">{{ number_format($paymentStats['paid_revenue']) }}d</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Booking trung bình</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($paymentStats['average_paid_booking']) }}d</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Bàn</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $tableStats['total'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Người dùng</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $userStats['total_users'] }}</p>
                </div>
            </section>

            <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Báo cáo theo khu vực</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-3">Khu vực</th>
                                    <th class="p-3">Booking</th>
                                    <th class="p-3">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($locationBreakdown as $item)
                                    <tr class="border-b">
                                        <td class="p-3">{{ $item->location ?: 'N/A' }}</td>
                                        <td class="p-3">{{ $item->booking_count }}</td>
                                        <td class="p-3 font-semibold">{{ number_format($item->revenue) }}d</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="p-4 text-center text-gray-500">Không có dữ liệu.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Top bàn</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-3">Bàn</th>
                                    <th class="p-3">Khu vực</th>
                                    <th class="p-3">Booking</th>
                                    <th class="p-3">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topTables as $item)
                                    <tr class="border-b">
                                        <td class="p-3 font-semibold">{{ $item->table_name }}</td>
                                        <td class="p-3">{{ $item->location }}</td>
                                        <td class="p-3">{{ $item->booking_count }}</td>
                                        <td class="p-3">{{ number_format($item->revenue) }}d</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-gray-500">Không có dữ liệu.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 xl:col-span-2">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Top khách hàng</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-3">Khách hàng</th>
                                    <th class="p-3">Email</th>
                                    <th class="p-3">Booking</th>
                                    <th class="p-3">Chi tiêu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $customer)
                                    <tr class="border-b">
                                        <td class="p-3 font-semibold">{{ $customer->username }}</td>
                                        <td class="p-3">{{ $customer->email }}</td>
                                        <td class="p-3">{{ $customer->booking_count }}</td>
                                        <td class="p-3">{{ number_format($customer->spent) }}d</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-gray-500">Không có dữ liệu.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>
