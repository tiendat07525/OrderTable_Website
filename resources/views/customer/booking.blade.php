<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt bàn - Golden Spoons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar-menu {
            transition: all 0.3s ease;
        }

        .sidebar-menu:hover {
            background-color: rgba(139, 92, 246, 0.1);
            border-left: 3px solid rgb(139, 92, 246);
            padding-left: calc(1.5rem - 3px);
        }

        .table-feature {
            transition: all 0.3s ease;
        }

        .table-feature:hover {
            transform: translateY(-4px);
        }

        .time-slot {
            transition: all 0.2s ease;
        }

        .time-slot:hover {
            background-color: rgb(139, 92, 246);
            color: white;
        }

        .time-slot.active {
            background-color: rgb(139, 92, 246);
            color: white;
        }

        .date-btn {
            transition: all 0.2s ease;
        }

        .date-btn:hover {
            background-color: rgb(139, 92, 246);
            color: white;
        }

        .date-btn.active {
            background-color: rgb(139, 92, 246);
            color: white;
        }

        #userMenuContainer {
            position: relative;
        }

        #userDropdown {
            padding-top: 8px;
        }

        #userDropdown::before {
        content: "";
        position: absolute;
        top: -8px;
        left: 0;
        right: 0;
        height: 8px;
        background: transparent;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-100">
    @include('customer.header')

    <div class="flex pt-16 min-h-screen gap-0">
        <main class="ml-20 flex-1 p-8 mt-8 mr-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <div>
                        <div class="inline-block mb-4 px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded text-xs font-semibold uppercase">
                            TRẠNG THÁI TRỰC TIẾP / CHỌN BÀN / {{ $table?->location ?? 'Sảnh Chính' }}
                        </div>
                        <h1 class="text-4xl font-bold mb-2">Đặt bàn của bạn</h1>
                    </div>

                    <div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700">
                        <div class="relative h-96 bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center overflow-hidden">
                            @if($table->image)
                                <img src="{{ asset('storage/' . $table->image) }}" 
                                    class="w-full h-full object-cover" alt="">
                            @else
                                <img src="{{ asset('images/default.png') }}"
                                    class="w-full h-full object-cover" alt="">
                            @endif
                        </div>

                        <div class="p-8 bg-slate-800">
                            <h2 class="text-2xl font-bold mb-2">{{ $table?->name ?? 'Bàn Cao Cấp' }}</h2>
                            <p class="text-slate-400 text-sm mb-6">{{ $table?->location ?? 'Sảnh Chính' }}, Bàn {{ $table?->id ?? '12' }}</p>

                            <div class="flex items-center gap-4 mb-6 text-sm">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
                                    </svg>
                                    <span>{{ $table?->capacity ?? '4' }} - {{ ($table?->capacity ?? 4) + 2 }} NGƯỜI</span>
                                </div>
                            </div>

                            <p class="text-slate-300 text-sm leading-relaxed mb-6">
                                @if($table)
                                Trải nghiệm buổi tối của bạn tại {{ $table->name }}. Không gian hoàn hảo này mang đến bầu không khí ấm cúng kết hợp với các tiện nghi hiện đại cho trải nghiệm ẩm thực tối ưu.
                                @else
                                Trải nghiệm buổi tối của bạn trong một trong những không gian tinh tế nhất của chúng tôi. Bàn Cao Cấp mang đến bầu không khí ấm cúng kết hợp với các tiện nghi hiện đại cho trải nghiệm ẩm thực tối ưu.
                                @endif
                            </p>

                            <div class="grid grid-cols-3 gap-4">
                                <div class="table-feature bg-slate-700/50 border border-slate-600 rounded-lg p-4 text-center hover:border-violet-500 hover:bg-slate-700">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-xs font-semibold">Không gian</p>
                                    <p class="text-xs text-slate-500 mt-1">Sang trọng & riêng tư</p>
                                </div>
                                <div class="table-feature bg-slate-700/50 border border-slate-600 rounded-lg p-4 text-center hover:border-violet-500 hover:bg-slate-700">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 15.464a5 5 0 010-7.072m2.828 2.828a7 7 0 010 9.9M5 12a7 7 0 009.9 0m-2.828-2.828a5 5 0 010 7.072M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="text-xs font-semibold">Trải nghiệm</p>
                                    <p class="text-xs text-slate-500 mt-1">Ẩm thực cao cấp</p>
                                </div>
                                <div class="table-feature bg-slate-700/50 border border-slate-600 rounded-lg p-4 text-center hover:border-violet-500 hover:bg-slate-700">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-xs font-semibold">Phục vụ</p>
                                    <p class="text-xs text-slate-500 mt-1">Chuyên nghiệp & tận tâm</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('customer.booking.store', $table->id ?? 0) }}" method="POST" class="space-y-6" id="booking-form">
                        @csrf

                    <div class="bg-slate-800 rounded-2xl p-8 border border-slate-700">
                        <h3 class="text-lg font-bold mb-6">Thông tin khách hàng</h3>

                        <div class="space-y-4 mb-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-2">HỌ VÀ TÊN</label>
                                    <input  type="text" name="username"
                                            placeholder="Trịnh Trần Phương Tuấn"
                                            class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-violet-500"
                                            value="{{ auth()->user()->username ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-2">EMAIL</label>
                                    <input  type="email" name="email"
                                            placeholder="alex@sterling.corp"
                                            class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-violet-500"
                                            value="{{ auth()->user()->email ?? '' }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-2">SỐ ĐIỆN THOẠI</label>
                                <input type="text"  name="phone"
                                    placeholder="0123456789"
                                    class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-violet-500"
                                    value="{{ auth()->user()->phone ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700 sticky top-24">
                        <h3 class="text-lg font-bold mb-6">Chọn thời gian</h3>
                            @if($table)
                            <input type="hidden" name="table_id" value="{{ $table->id }}">
                            @endif

                            <div class="mb-6">
                                <label class="block text-xs font-semibold text-slate-300 mb-3">CHỌN NGÀY</label>
                                <div class="grid grid-cols-5 gap-2">
                                @for($i = 0; $i < 5; $i++)
                                    @php
                                        $date = \Carbon\Carbon::today()->addDays($i);
                                    @endphp
                                    <button type="button"
                                        class="date-btn bg-slate-700 text-slate-400 py-2 rounded text-xs font-semibold"
                                        data-date="{{ $date->format('Y-m-d') }}">
                                        
                                        {{ $date->format('d/m') }}<br>
                                        {{ $date->format('D') }}
                                    </button>
                                @endfor
                                </div>
                                <input type="hidden" name="booking_date" id="booking_date">
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-semibold text-slate-300 mb-3">CHỌN GIỜ</label>

                                <div class="grid grid-cols-3 gap-2">
                                    @foreach(config('booking.time_slots') as $time)
                                        <button type="button"
                                            class="time-slot bg-slate-700 text-slate-400 py-2 rounded text-xs font-semibold relative"
                                            data-time="{{ $time }}">

                                            {{ $time }}

                                            <span class="status hidden text-[10px] text-red-400 block">
                                                Đã đặt
                                            </span>
                                        </button>
                                    @endforeach
                                </div>

                                <input type="hidden" name="booking_time" id="booking_time">
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-semibold text-slate-300 mb-2">SỐ KHÁCH</label>
                                <select name="guest_count" class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 focus:outline-none focus:border-violet-500">
                                    <option value="" disabled selected>Chọn số khách</option>
                                    {{-- TODO: Production improvement - keep customer selectable guest count aligned with server-side table capacity validation. --}}
                                    @for($i = 1; $i <= $table->capacity; $i++)
                                        <option value="{{ $i }}">{{ $i }} người</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-semibold text-slate-300 mb-2">YÊU CẦU ĐẶC BIỆT</label>
                                <textarea name="special_requests" placeholder="Nhập yêu cầu đặc biệt của bạn..." class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-violet-500 h-20 resize-none"></textarea>
                            </div>

                            <input type="hidden" name="total_price" id="total_price">

                            <div class="bg-slate-900 rounded-lg p-4 mb-6 space-y-2 border border-slate-600">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-400">Đặt cọc</span>
                                    <span class="text-white font-semibold">{{ number_format($table->price)}}đ</span>
                                </div>
                                <div class="border-t border-slate-700 pt-2 mt-2 flex justify-between">
                                    <span class="text-sm font-semibold text-slate-300">TỔNG CỘNG</span>
                                    <span class="text-lg font-bold text-violet-400">{{ number_format($table->price)}}đ</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold py-3 rounded-lg transition transform hover:scale-105 mb-4 text-sm">
                                XÁC NHẬN ĐẶT CHỖ
                            </button>

                            <p class="text-xs text-slate-500 text-center">
                                Bảo mật thông tin. Có thể hủy trong vòng 24h.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>

        document.getElementById('booking-form').addEventListener('submit', function(e) {
            const date = document.getElementById('booking_date').value;
            const time = document.getElementById('booking_time').value;
            const guestCount = document.querySelector('select[name="guest_count"]').value;
            if (!date || !time || !guestCount) {
                e.preventDefault();
                alert('Vui lòng chọn đầy đủ thông tin.');
            }
        });

        document.querySelectorAll('.date-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                document.querySelectorAll('.date-btn').forEach(b => {
                    b.classList.remove('active', 'bg-violet-600', 'text-white');
                    b.classList.add('bg-slate-700', 'text-slate-400');
                });

                this.classList.add('active', 'bg-violet-600', 'text-white');

                const date = this.dataset.date;
                document.getElementById('booking_date').value = date;

                document.getElementById('booking_time').value = '';

                document.querySelectorAll('.time-slot').forEach(b => {
                    b.classList.remove('active', 'bg-violet-600', 'text-white');
                    b.classList.add('bg-slate-700', 'text-slate-400');
                    b.disabled = false;
                });

                fetch(`/customer/booking/booked-times?table_id={{ $table->id ?? 0 }}&date=${date}`)
                    .then(res => res.json())
                    .then(data => {

                        console.log("BOOKED DATA:", data);

                        const booked = data.map(item => {
                            return (item.booking_time ?? item).substring(0, 5);
                        });

                        document.querySelectorAll('.time-slot').forEach(btn => {
                            const time = btn.dataset.time;
                            const status = btn.querySelector('.status');

                            if (booked.includes(time)) {
                                btn.dataset.booked = "1";

                                btn.classList.add('opacity-60', 'cursor-not-allowed');
                                btn.classList.remove('hover:bg-violet-600');

                                if (status) status.classList.remove('hidden');
                            } else {
                                btn.dataset.booked = "0";

                                btn.classList.remove('opacity-60', 'cursor-not-allowed');
                                btn.classList.add('hover:bg-violet-600');

                                if (status) status.classList.add('hidden');
                            }
                        });
                    })
                    .catch(err => {
                        console.error("Fetch error:", err);
                    });
            });
        });

        document.querySelectorAll('.time-slot').forEach(btn => {
            btn.addEventListener('click', function (e) {

                const selectedTime = this.dataset.time;

                if (this.dataset.booked === "1") {
                    alert('Khung giờ này đã có người đặt, vui lòng chọn giờ khác');
                    return;
                }

                e.preventDefault();

                document.querySelectorAll('.time-slot').forEach(b => {
                    b.classList.remove('active', 'bg-violet-600', 'text-white');
                    b.classList.add('bg-slate-700', 'text-slate-400');
                });

                this.classList.add('active', 'bg-violet-600', 'text-white');
                this.classList.remove('bg-slate-700', 'text-slate-400');

                document.getElementById('booking_time').value = selectedTime;
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("userMenuContainer");
            const dropdown = document.getElementById("userDropdown");
            const button = document.getElementById("userMenuButton");
            let hideTimeout;

            container.addEventListener("mouseenter", function () {
                clearTimeout(hideTimeout);
                dropdown.classList.remove("hidden");
            });

            dropdown.addEventListener("mouseenter", function () {
                clearTimeout(hideTimeout);
            });

            container.addEventListener("mouseleave", function () {
                hideTimeout = setTimeout(() => {
                    dropdown.classList.add("hidden");
                }, 100);
            });

            dropdown.addEventListener("mouseleave", function () {
                hideTimeout = setTimeout(() => {
                    dropdown.classList.add("hidden");
                }, 100);
            });

            button.addEventListener("click", function (e) {
                e.stopPropagation();
                dropdown.classList.toggle("hidden");
            });

            document.addEventListener("click", function (e) {
                if (!container.contains(e.target)) {
                    dropdown.classList.add("hidden");
                }
            });
        });
    </script>
    @include('customer.footer')
</body>

</html>
