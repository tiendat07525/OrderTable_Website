<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        $paymentLabels = [
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán lỗi',
        ];

        $statusClasses = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'no_show' => 'bg-gray-200 text-gray-700',
        ];

        $paymentClasses = [
            'unpaid' => 'bg-gray-100 text-gray-700',
            'paid' => 'bg-emerald-100 text-emerald-800',
            'failed' => 'bg-red-100 text-red-800',
        ];

        $exportUrl = route('admin.bookings.export', request()->except('page'));
    @endphp

    <div class="flex min-h-screen">
        @include('admin.sidebar')

        <main class="flex-1 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý booking</h1>
                    <p class="text-sm text-gray-500 mt-1">Theo dõi, lọc, cập nhật trạng thái và xử lý booking.</p>
                </div>

                <a href="{{ $exportUrl }}"
                   class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Xuất Excel
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Tổng đơn hôm nay</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['today'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Chờ xử lý</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Đã xác nhận</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['confirmed'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Đã hủy</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Sắp đến giờ</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['upcoming'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Doanh thu đã thanh toán</p>
                    <p class="text-xl font-bold text-emerald-600">{{ number_format($stats['paid_revenue'] ?? 0) }}d</p>
                </div>
            </section>

            <section class="bg-white rounded-lg shadow-sm p-5 mb-6">
                <form method="GET" action="{{ route('admin.bookings') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tìm kiếm</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                               placeholder="Tên khách hàng, email, số điện thoại"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Trạng thái</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Tất cả</option>
                            @foreach($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Thanh toán</label>
                        <select name="payment_status" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Tất cả</option>
                            @foreach($paymentLabels as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['payment_status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ngày đặt bàn</label>
                        <input type="date" name="booking_date" value="{{ $filters['booking_date'] ?? '' }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sắp xếp</label>
                        <select name="sort" class="w-full border rounded-lg px-3 py-2">
                            @foreach(['created_at' => 'Ngày tạo', 'date' => 'Ngày đặt', 'time' => 'Giờ đặt', 'status' => 'Trạng thái', 'payment_status' => 'Thanh toán', 'total_price' => 'Tổng tiền'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['sort'] ?? 'created_at') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3 flex items-end gap-3">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Lọc dữ liệu
                        </button>
                        <a href="{{ route('admin.bookings') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Reset bộ lọc
                        </a>
                    </div>
                </form>
            </section>

            <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="p-3">Mã</th>
                                <th class="p-3">Khách hàng</th>
                                <th class="p-3">Bàn</th>
                                <th class="p-3">Ngày/Giờ</th>
                                <th class="p-3">Trạng thái</th>
                                <th class="p-3">Thanh toán</th>
                                <th class="p-3">Tổng tiền</th>
                                <th class="p-3">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                @php
                                    $bookingDateTime = \Carbon\Carbon::parse($booking->date->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($booking->time)->format('H:i:s'));
                                    $isUpcomingSoon = in_array($booking->status, ['pending', 'confirmed']) && $bookingDateTime->isBetween(now(), now()->addHour());
                                @endphp
                                <tr class="border-b align-top hover:bg-gray-50">
                                    <td class="p-3 font-semibold">
                                        #{{ $booking->id }}
                                        @if($isUpcomingSoon)
                                            <span class="block mt-1 text-xs text-orange-600 font-bold">Sắp đến giờ</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <div class="font-semibold text-gray-900">{{ $booking->user->username ?? $booking->user->name ?? 'Khách' }}</div>
                                        <div class="text-gray-500">{{ $booking->phone }}</div>
                                        <div class="text-gray-500">{{ $booking->email }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-semibold">{{ $booking->table->name ?? 'N/A' }}</div>
                                        <div class="text-gray-500">{{ $booking->table->location ?? '' }}</div>
                                        <div class="text-gray-500">{{ $booking->guest_count }} khách</div>
                                    </td>
                                    <td class="p-3">
                                        <div>{{ $booking->date->format('d/m/Y') }}</div>
                                        <div class="font-semibold">{{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}</div>
                                        <div class="text-gray-500">Tạo: {{ $booking->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusLabels[$booking->status] ?? $booking->status }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $paymentClasses[$booking->payment_status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $paymentLabels[$booking->payment_status] ?? $booking->payment_status }}
                                        </span>
                                        @if($booking->paid_at)
                                            <div class="text-xs text-gray-500 mt-1">{{ $booking->paid_at->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3 font-semibold">{{ number_format($booking->total_price) }}d</td>
                                    <td class="p-3">
                                        <details>
                                            <summary class="cursor-pointer text-blue-600 font-semibold">Xử lý</summary>
                                            <div class="mt-4 w-[520px] max-w-[80vw] space-y-4 bg-gray-50 border rounded-lg p-4">
                                                <form method="POST" action="{{ route('admin.bookings.status', $booking->id) }}"
                                                      onsubmit="return confirm('Xác nhận cập nhật trạng thái booking này?')"
                                                      class="grid grid-cols-2 gap-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div>
                                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Trạng thái mới</label>
                                                        <select name="status" class="w-full border rounded-lg px-3 py-2">
                                                            @foreach($statusLabels as $value => $label)
                                                                <option value="{{ $value }}" @selected($booking->status === $value)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Lý do hủy</label>
                                                        <input name="cancel_reason" value="{{ $booking->cancel_reason }}"
                                                               class="w-full border rounded-lg px-3 py-2">
                                                    </div>
                                                    <div class="col-span-2">
                                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ghi chú lịch sử</label>
                                                        <input name="note" placeholder="Ví dụ: Khách gọi xác nhận lúc 18:30"
                                                               class="w-full border rounded-lg px-3 py-2">
                                                    </div>
                                                    <div class="col-span-2">
                                                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                            Cập nhật trạng thái
                                                        </button>
                                                    </div>
                                                </form>

                                                <div class="flex flex-wrap gap-2">
                                                    <form method="POST" action="{{ route('admin.bookings.resend-email', $booking->id) }}"
                                                          onsubmit="return confirm('Gửi lại email xác nhận cho booking này?')">
                                                        @csrf
                                                        <button class="px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                                            Gửi lại email
                                                        </button>
                                                    </form>
                                                </div>

                                                <div>
                                                    <p class="font-semibold text-sm mb-2">Lịch sử trạng thái</p>
                                                    <div class="space-y-2 max-h-44 overflow-y-auto">
                                                        @forelse($booking->statusHistories->sortByDesc('created_at') as $history)
                                                            <div class="border rounded-lg bg-white p-2 text-xs">
                                                                <div class="font-semibold">
                                                                    {{ $statusLabels[$history->from_status] ?? $history->from_status ?? 'Mới' }}
                                                                    ->
                                                                    {{ $statusLabels[$history->to_status] ?? $history->to_status }}
                                                                </div>
                                                                <div class="text-gray-500">
                                                                    {{ $history->created_at->format('d/m/Y H:i') }}
                                                                    @if($history->changedBy)
                                                                        Cập nhật bởi {{ $history->changedBy->username }}
                                                                    @endif
                                                                </div>
                                                                @if($history->note)
                                                                    <div class="mt-1">{{ $history->note }}</div>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <p class="text-xs text-gray-500">Chưa có lịch sử cập nhật.</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-6 text-center text-gray-500">Không có booking phù hợp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t">
                    {{ $bookings->links() }}
                </div>
            </section>
        </main>
    </div>
</body>

</html>
