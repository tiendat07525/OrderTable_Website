<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đặt Bàn - Luminous Epicure</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800">
    @include('customer.header')

    <main class="pt-28 max-w-7xl mx-auto px-4 py-10 h-screen">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-bold tracking-wider
                        {{ $booking->status == 'pending' ? 'text-yellow-600' : '' }}
                        {{ $booking->status == 'confirmed' ? 'text-green-600' : '' }}
                        {{ $booking->status == 'cancelled' ? 'text-red-600' : '' }}">
                        
                        <i class="fas fa-check-circle mr-2"></i>

                        @if($booking->status == 'pending')
                            SẮP TỚI
                        @elseif($booking->status == 'confirmed')
                            HOÀN THÀNH
                        @else
                            ĐÃ HỦY
                        @endif
                    </span>
                </div>
                
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('customer.booking.cancel', $booking->id) }}"
                        onsubmit="return confirm('Bạn có chắc muốn hủy đặt bàn?')">
                        @csrf

                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-white text-red-600 border border-red-300 rounded-lg hover:bg-red-50 transition">
                            <i class="fas fa-times-circle text-red-600"></i>
                            <span>Hủy đặt bàn</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="mb-4">
            <a href="{{ route('customer.history') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </a>
        </div>
            <h1 class="text-4xl font-bold text-gray-900">Chi tiết đặt bàn</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex gap-4 mb-6">
                        @php
                            $image = $booking->table->image;
                        @endphp

                        <img src="{{ ($image && file_exists(public_path('storage/'.$image))) 
                                ? asset('storage/'.$image) 
                                : asset('images/default.png') }}"
                        class="w-20 h-20 rounded-full object-cover">
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $booking->table->name ?? 'Golden Spoons' }}</h2>
                            <p class="text-sm text-gray-600 flex items-center gap-1">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                {{ $booking->table->location ?? 'Sảnh chính' }}
                            </p>
                            <p class="text-xs text-gray-500 font-semibold mb-1">NGÀY</p>
                            {{-- TODO: Production improvement - display booking date from the date column instead of parsing the time column. --}}
                            <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold mb-1">GIỜ</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}-{{ \Carbon\Carbon::parse($booking->time)->addHour()->format('H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 font-semibold mb-2 tracking-widest">KHÁCH HÀNG</p>
                            <p class="text-4xl font-bold text-blue-600 mb-2">{{ $booking->guest_count ?? '4' }}</p>
                            <p class="text-sm text-gray-600">Người</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-xs text-gray-500 font-semibold mb-3 tracking-widest">VỊ TRÍ BÀN</p>
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-armchair text-blue-600 text-lg"></i>
                            <p class="text-2xl font-bold text-gray-900">{{ $booking->table->name ?? 'Bàn T04' }}</p>
                        </div>
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold">{{ $booking->table->location ?? 'sảnh chính' }}</span> - {{ $booking->guest_count}} chỗ ngồi
                        </p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-comments text-blue-600"></i>
                        GHI CHÚ & YÊU CẦU ĐẶC BIỆT
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4 italic text-gray-700">
                        <p>{{ $booking->special_requests ?? 'Không có yêu cầu' }}</p>
                    </div>
                </div>
            </div>
                <div class="bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl shadow-md p-6 text-white">
                    <h3 class="text-lg font-bold mb-6 tracking-wider">THÔNG TIN THANH TOÁN</h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center border-b border-white/20 pb-3">
                            <span class="text-sm">Tổng tiền dự kiến</span>
                            <span class="font-bold text-xl">{{ number_format($booking->total_price ?? 2450000, 0, ',', '.') }}đ</span>
                        </div>

                        <div class="flex justify-between items-center border-b border-white/20 pb-3">
                            <span class="text-sm">Trạng thái</span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-{{ $booking->payment_status === 'paid' ? 'check-circle text-green-300' : 'clock text-yellow-300' }}"></i>
                                <span class="text-xs font-bold">{{ $booking->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</span>
                            </span>
                        </div>

                        <div class="flex justify-between items-center pt-3">
                            <span class="text-sm">Phương thức</span>
                            <span class="flex items-center gap-1 text-sm">
                                <i class="fas fa-credit-card"></i>
                                <span>{{ $booking->payment_method === 'bank_transfer' ? 'Chuyển khoản' : 'Tiền mặt' }}</span>
                            </span>
                        </div>
                    </div>

                    @if($booking->payment_status !== 'paid' && $booking->status !== 'cancelled')
                        <a href="{{ route('customer.booking.confirm', $booking->id) }}"
                            class="w-full block text-center bg-yellow-400 text-black font-bold py-3 rounded-lg hover:bg-yellow-300 transition">
                            <i class="fas fa-credit-card mr-2"></i>
                            Thanh toán ngay
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </main>
    @include('customer.footer')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("userMenuContainer");
            const dropdown = document.getElementById("userDropdown");
            let timeout;

            container.addEventListener("mouseenter", function () {
                clearTimeout(timeout);
                dropdown.classList.remove("hidden");
            });

            container.addEventListener("mouseleave", function () {
                timeout = setTimeout(() => {
                    dropdown.classList.add("hidden");
                }, 200);
            });
            
            const button = document.getElementById("userMenuButton");
            button.addEventListener("click", function (e) {
                e.stopPropagation();
                dropdown.classList.toggle("hidden");
            });
        });
    </script>
</body>

</html>
