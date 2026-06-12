<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Xác Nhận Đặt Bàn - Luminous Epicure</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
</head>
<body class="bg-gray-50">

    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gray-800 to-black relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=1000&fit=crop"
                alt="Restaurant" class="w-full h-screen object-cover opacity-80">
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 ">
            <div class="w-full max-w-md bg-white rounded-2xl p-8 border border-gray-200 shadow-lg relative">
                <button onclick="window.location.href = '{{ route('customer.history') }}'" class="absolute top-6 right-6 text-gray-400 hover:text-gray-900">
                    <i class="fas fa-times text-2xl"></i>
                </button>

                <div class="mb-8">
                    <p class="text-sm font-bold text-gray-500 tracking-widest mb-2">BƯỚC CUỐI CÙNG</p>
                    <h1 class="text-4xl font-bold text-gray-900">Xác nhận thông tin đặt bàn</h1>
                    <p class="text-gray-600 mt-3 text-sm leading-relaxed">
                        Vui lòng kiểm tra kỹ các thông tin bên dưới trước khi hoàn tất yêu cầu của bạn.
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-6 mb-8 border border-gray-100 shadow-sm">
                    <div class="mb-6">
                        <p class="text-xs text-gray-500 font-semibold tracking-widest mb-2">TÊN KHÁCH HÀNG</p>
                        <p class="text-lg font-bold text-gray-900">{{ Auth::user()->name ?? $booking->phone ?? 'Khách Hàng' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-xs text-gray-500 font-semibold tracking-widest mb-2">NGÀY & GIỜ</p>
                            {{-- TODO: Production improvement - display booking date from the date column instead of parsing the time column. --}}
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</p>
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 font-semibold tracking-widest mb-2">SỐ LƯỢNG KHÁCH</p>
                            <p class="text-sm font-bold text-gray-900">{{ $booking->guest_count }} Người <i class="fas fa-users text-gray-400 ml-2"></i></p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 font-semibold tracking-widest mb-2">VỊ TRÍ BÀN</p>
                        <p class="text-sm font-bold text-gray-900">{{ $booking->table->name ?? 'Bàn T04' }} - {{ $booking->table->location ?? 'View cửa sổ' }}</p>
                    </div>

                    @if($booking->special_requests)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <p class="text-xs text-gray-500 font-semibold tracking-widest mb-2">YÊU CẦU ĐẶC BIỆT</p>
                        <p class="text-sm text-gray-600 italic">{{ $booking->special_requests }}</p>
                    </div>
                    @endif
                </div>

                <div class="space-y-3">
                    <button onclick="openPaymentModal()" class="w-full bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold py-4 rounded-xl transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        Xác nhận đặt bàn
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" data-booking-id="{{ $booking->id }}" data-total-price="{{ $booking->total_price ?? 0 }}" data-transfer-content="BOOKING-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-violet-600 to-purple-600 text-white p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold opacity-90">THANH TOÁN QUA CHUYỂN KHOẢN</p>
                    <h2 class="text-2xl font-bold mt-1">Quét mã QR để thanh toán</h2>
                </div>
                <button onclick="closePaymentModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <div class="flex flex-col items-center">
                        <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl mb-4 flex justify-center">
                            <img 
                                src="{{ $vietQrUrl }}"
                                alt="QR thanh toán"
                                class="w-64 h-64 rounded-lg"
                            >
                        </div>
                        <div
                            id="payment-status"
                            class="mt-4 px-4 py-2 rounded-lg bg-yellow-100 text-yellow-700 text-sm font-bold"
                        >
                            Đang chờ thanh toán...
                        </div>
                        <p class="text-sm text-slate-400 text-center">
                            Quét mã QR bằng ứng dụng ngân hàng của bạn
                        </p>
                    </div>

                    <div>
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-100 mb-4 flex items-center gap-2 text-slate-600">
                                <i class="fas fa-university text-violet-400"></i>
                                Thông Tin Ngân Hàng
                            </h3>

                            <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold mb-1">NGÂN HÀNG</p>
                                    {{-- TODO: Production improvement - render bank details from config/env instead of hardcoded account data. --}}
                                    <p class="text-lg font-bold text-slate-100">{{ config('payment.bank_code') }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-400 font-semibold mb-1">CHỦ TÀI KHOẢN</p>
                                    <p class="text-lg font-bold text-slate-100">{{ config('payment.account_name') }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-400 font-semibold mb-1">SỐ TÀI KHOẢN</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-lg font-bold text-slate-100 font-mono">{{ config('payment.account_no') }}</p>
                                        <button onclick="copyToClipboard('{{ config('payment.account_no') }}')" 
                                            class="text-violet-400 hover:text-violet-300">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-700">
                    <div class="bg-gradient-to-r from-violet-600/10 to-purple-600/10 border border-slate-700 rounded-xl p-6 flex justify-between">
                        <div>
                            <p class="text-sm text-slate-400 font-semibold mb-1">TỔNG TIỀN</p>
                            <p class="text-3xl font-bold text-slate-700">
                                {{ number_format($booking->total_price, 0, ',', '.') }}đ
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-4">

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <div class="flex items-center gap-3">

                            <i class="fas fa-clock text-yellow-500 text-xl"></i>

                            <div>
                                <p class="font-bold text-yellow-700">
                                    Đang chờ thanh toán
                                </p>

                                <p class="text-sm text-yellow-600">
                                    Hệ thống sẽ tự động xác nhận
                                    sau khi bạn chuyển khoản thành công
                                </p>
                            </div>
                        </div>
                    </div>
                    <button onclick="closePaymentModal()" class="w-full bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 rounded-xl">
                        Đóng
                    </button>

                </div>
            </div>
        </div>
    </div>
        <div id="toast-success" class="hidden fixed top-6 right-6 bg-green-600 text-white px-6 py-4 rounded-xl shadow-lg z-50">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toast-message"></span>
    </div>

    <script>
        function copyToClipboard(value) {
            navigator.clipboard.writeText(value);
            showSuccessToast('Da copy thong tin thanh toan');
        }

        const paymentModal =
            document.getElementById(
                'paymentModal'
            );

        const bookingId =
            paymentModal.dataset.bookingId;

        const statusBox =
            document.getElementById(
                'payment-status'
            );

        let paymentInterval = null;

        let isPaid = false;

        function openPaymentModal() {

            paymentModal.classList.remove(
                'hidden'
            );

            document.body.style.overflow =
                'hidden';

            startPaymentPolling();
        }

        function closePaymentModal() {

            paymentModal.classList.add(
                'hidden'
            );

            document.body.style.overflow =
                'auto';

            stopPaymentPolling();
        }

        function startPaymentPolling() {

            if (paymentInterval) return;

            checkPaymentStatus();

            paymentInterval = setInterval(
                checkPaymentStatus,
                3000
            );
        }

        function stopPaymentPolling() {

            if (paymentInterval) {

                clearInterval(
                    paymentInterval
                );

                paymentInterval = null;
            }
        }

        async function checkPaymentStatus() {

            if (isPaid) return;

            try {

                const response = await fetch(
                    `/customer/booking/payment-status/${bookingId}`,
                    {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!response.ok) {

                    throw new Error(
                        `HTTP ${response.status}`
                    );
                }

                const data =
                    await response.json();

                console.log(
                    'Payment status:',
                    data
                );

                if (
                    data.payment_status ===
                    'paid'
                ) {

                    isPaid = true;

                    stopPaymentPolling();

                    statusBox.className =
                        'mt-4 px-4 py-2 rounded-lg bg-green-100 text-green-700 text-sm font-bold';

                    statusBox.innerHTML =
                        '<i class="fas fa-check-circle mr-2"></i>Thanh toán thành công';

                    showSuccessToast(
                        'Thanh toán thành công'
                    );

                    setTimeout(() => {

                        window.location.href =
                            `/customer/booking/detail/${bookingId}`;

                    }, 1500);

                    return;
                }

                statusBox.className =
                    'mt-4 px-4 py-2 rounded-lg bg-yellow-100 text-yellow-700 text-sm font-bold';

                statusBox.innerHTML =
                    '<i class="fas fa-clock mr-2"></i>Đang chờ thanh toán...';

            } catch (error) {

                console.error(
                    'Payment check error:',
                    error
                );

                statusBox.className =
                    'mt-4 px-4 py-2 rounded-lg bg-red-100 text-red-700 text-sm font-bold';

                statusBox.innerHTML =
                    '<i class="fas fa-exclamation-circle mr-2"></i>Lỗi kiểm tra thanh toán';
            }
        }

        function showSuccessToast(message) {

            const toast =
                document.getElementById(
                    'toast-success'
                );

            const text =
                document.getElementById(
                    'toast-message'
                );

            text.innerText = message;

            toast.classList.remove(
                'hidden'
            );

            setTimeout(() => {

                toast.classList.add(
                    'hidden'
                );

            }, 2000);
        }

        document.addEventListener(
            'keydown',
            (e) => {

                if (
                    e.key === 'Escape'
                ) {

                    closePaymentModal();
                }
            }
        );

    </script>
</body>

</html>
