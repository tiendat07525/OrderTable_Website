<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body class="bg-gray-50">
    @php
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
            'completed' => 'Hoàn thành',
            'no_show' => 'Khách không đến',
        ];

        $statusClasses = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'no_show' => 'bg-gray-200 text-gray-700',
        ];

        // TODO: Production improvement - keep dashboard chart visible even when there are no bookings yet.
        $bookingChartLabels = ['Đã xác nhận', 'Chờ xác nhận', 'Đã hủy', 'Đã hoàn thành', 'Không đến'];
        $bookingChartData = [
            $statusCounts['confirmed'] ?? 0,
            $statusCounts['pending'] ?? 0,
            $statusCounts['cancelled'] ?? 0,
            $statusCounts['completed'] ?? 0,
            $statusCounts['no_show'] ?? 0,
        ];
        $bookingChartColors = ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#6b7280'];

        if (array_sum($bookingChartData) === 0) {
            $bookingChartLabels = ['Chưa có dữ liệu'];
            $bookingChartData = [1];
            $bookingChartColors = ['#e5e7eb'];
        }
    @endphp

    <div class="flex min-h-screen">
        @include('admin.sidebar')

        <main class="flex-1 p-8">
            <div class="mb-8 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg shadow-lg p-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Bảng điều khiển</h1>
                    <p class="text-lg text-blue-100">Tổng quan booking, bàn, thanh toán và việc cần xử lý.</p>
                </div>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Đăng xuất
                    </button>
                </form>
            </div>


            <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Booking hôm nay</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayBookings }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Đã xác nhận</p>
                    <p class="text-2xl font-bold text-green-600">{{ $todayConfirmed }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Bàn trống hôm nay</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $availableTables }}/{{ $totalTables }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Doanh thu hôm nay</p>
                    <p class="text-xl font-bold text-emerald-600">{{ number_format($todayPaidRevenue) }}d</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Thanh toán</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayPaidBookings }} đã thanh toán</p>
                </div>
            </section>

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Doanh thu 7 ngày</h2>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Trạng thái booking</h2>
                    <div class="h-64">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Booking mới nhất</h2>
                        <a href="{{ route('admin.bookings') }}" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-3">Mã</th>
                                    <th class="p-3">Khách/Bàn</th>
                                    <th class="p-3">Ngày giờ</th>
                                    <th class="p-3">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr class="border-b">
                                        <td class="p-3 font-semibold">#{{ $booking->id }}</td>
                                        <td class="p-3">
                                            <div>{{ $booking->user->username ?? $booking->user->name ?? 'Khách' }}</div>
                                            <div class="text-xs text-gray-500">{{ $booking->table->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="p-3">
                                            {{ $booking->date->format('d/m/Y') }}
                                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}</div>
                                        </td>
                                        <td class="p-3">
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ $statusLabels[$booking->status] ?? $booking->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-gray-500">Chưa có booking.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Timeline hôm nay</h2>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($todayTimeline as $booking)
                            <div class="flex items-start gap-3 border-l-4 border-blue-500 bg-gray-50 p-3 rounded-r-lg">
                                <div class="font-bold text-gray-900 w-14">{{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}</div>
                                <div class="flex-1">
                                    <div class="font-semibold">#{{ $booking->id }} - {{ $booking->table->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-600">{{ $booking->user->username ?? $booking->user->name ?? 'Khach' }} - {{ $booking->guest_count }} khach</div>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Hôm nay chưa có booking.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Doanh thu (trieu)',
                    data: @json($revenues),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(bookingsCtx, {
            type: 'doughnut',
            data: {
                labels: @json($bookingChartLabels),
                datasets: [{
                    data: @json($bookingChartData),
                    backgroundColor: @json($bookingChartColors),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
</body>

</html>
