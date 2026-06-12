<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Chọn bàn - Luminous Epicure</title>
</head>

<body class="bg-slate-950 text-slate-100">
    @include('customer.header')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 mt-16">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <aside class="lg:col-span-1">
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 sticky top-24">
                    <h3 class="text-lg font-bold mb-6">BỘ LỌC</h3>
                    @if(isset($error) && $error)
                        <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
                            {{ $error }}
                        </div>
                    @endif
                    <form method="GET" action="{{ route('customer.search') }}" class="space-y-6">
                        <div class="mb-8">
                            <h4 class="text-sm font-semibold text-slate-300 mb-4">Khu vực</h4>
                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="location" value="Sảnh chính" class="w-4 h-4" {{ request('location') === 'Sảnh chính' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm">Sảnh chính</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="location" value="Sân thượng" class="w-4 h-4" {{ request('location') === 'Sân thượng' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm">Sân thượng</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="location" value="Khu VIP" class="w-4 h-4" {{ request('location') === 'Khu VIP' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm">Khu VIP</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-sm font-semibold text-slate-300 mb-4">Số khách</h4>
                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="guest_count" value="1" class="w-4 h-4" {{ request('guest_count') === '1' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm">1 người</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="guest_count" value="2" class="w-4 h-4" {{ request('guest_count') === '2' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm">2 người</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="guest_count" value="3" class="w-4 h-4" {{ request('guest_count') === '3' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm">3 người</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="guest_count" value="4" class="w-4 h-4" {{ request('guest_count') === '4' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm">4 người</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="guest_count" value="5" class="w-4 h-4" {{ request('guest_count') === '5' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm">5+ người</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-violet-600 hover:bg-violet-700 text-white py-2 rounded-lg transition">
                            Áp dụng bộ lọc
                        </button>
                        <a href="{{ route('customer.booking.index') }}"
                            class="block text-center bg-slate-700 hover:bg-slate-600 text-white py-2 rounded-lg transition mt-3">
                            Hiển thị tất cả bàn
                        </a>
                    </form>
                </div>
            </aside>

            <main class="lg:col-span-3">
                <section>
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-2">Sơ đồ bàn trực tiếp</h2>
                        <p class="text-slate-400">Trạng thái bàn được cập nhật theo thời gian thực.</p>
                    </div>

                    @if(isset($noResult) && $noResult)
                        <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-4 rounded-lg mb-6 text-center">
                            Không có bàn phù hợp với lựa chọn của bạn!
                        </div>
                    @endif

                    <div class="flex gap-6 mb-8">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm text-slate-300">CÒN TRỐNG</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm text-slate-300">ĐÃ ĐẶT</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-slate-300">ĐANG SỬ DỤNG</span>
                        </div>
                    </div>

                @if($tables->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($tables as $table)
                        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                            <div class="h-48 flex items-center justify-center">
                                @if($table->image)
                                    <img src="{{ asset('storage/' . $table->image) }}" 
                                        class="w-full h-full object-cover" alt="">
                                @else
                                    <img src="{{ asset('images/default.png') }}"
                                        class="w-full h-full object-cover" alt="">
                                @endif
                            </div>

                            <div class="p-4">

                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="text-lg font-bold">
                                        {{ $table->name }}
                                    </h3>

                                    <span class="text-emerald-400 font-bold text-lg">
                                        {{ number_format($table->price) }}đ
                                    </span>
                                </div>

                                <div class="flex justify-between items-center mb-4">
                                    <p class="text-sm text-slate-400">
                                        {{ $table->capacity }} người
                                    </p>

                                    <p class="text-sm text-slate-300">
                                        {{ $table->location ?? 'Sảnh chính' }}
                                    </p>
                                </div>

                                @if($table->status === 'available')
                                <a href="{{ route('customer.booking.create', ['id' => $table->id]) }}"
                                    class="block text-center bg-green-600 hover:bg-violet-600 py-2 rounded transition font-medium">
                                    Đặt ngay
                                </a>
                                @elseif($table->status === 'reserved')
                                <button disabled class="w-full bg-yellow-600 py-2 rounded cursor-not-allowed text-slate-300">
                                    Đã đặt
                                </button>
                                @else
                                <button disabled class="w-full bg-red-600 py-2 rounded cursor-not-allowed text-slate-300">
                                    Đang sử dụng
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
                @if($tables instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-8 flex justify-center">
                        {{ $tables->links() }}
                    </div>
                @endif
                </section>
            </main>
        </div>
    </div>
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
    @include('customer.footer')
</body>

</html>