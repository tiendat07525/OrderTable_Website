<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Tài Khoản - Golden Spoons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800 ">
    @include('customer.header')

    <div class="pt-20 max-w-6xl mx-auto py-10 px-4 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="md:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl shadow-lg p-6 flex items-center gap-6">
                    <div class="w-20 h-20 rounded-full border-4 border-indigo-100 bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold uppercase">
                        {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->username, 0, 1)) }}
                    </div>

                    <div>
                        <h2 class="text-xl font-bold">
                            {{ auth()->user()->name ?? auth()->user()->username }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            Thành viên từ {{ auth()->user()->created_at->format('M Y') }}
                        </p>
                    </div>

                    <button onclick="openModal()"
                        class="ml-auto px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Chỉnh sửa
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="bg-white rounded-2xl shadow-lg p-4 flex items-center gap-4">
                        <i class="fas fa-envelope text-indigo-600"></i>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-4 flex items-center gap-4">
                        <i class="fas fa-phone text-indigo-600"></i>
                        <div>
                            <p class="text-sm text-gray-500">Số điện thoại</p>
                            <p class="font-medium">
                                {{ auth()->user()->phone ?? 'Chưa cập nhật' }}
                            </p>
                        </div>
                    </div>

                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 ">
                    <p class="text-sm text-gray-500 mb-2">Tiểu sử</p>
                    <p class="text-gray-700 italic">
                        {{ auth()->user()->bio ?? 'Chưa có thông tin' }}
                    </p>
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-2xl shadow-lg p-6 text-center border-2">
                    <i class="fas fa-check-circle text-600 text-3xl mb-3"></i>
                    <p class="text-sm text-gray-600">ĐƠN HOÀN THÀNH</p>
                    <p class="text-3xl font-bold text-600">
                        {{ $completedBookings }}
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 text-center border-2">
                    <i class="fas fa-calendar-check text-500 text-3xl mb-3"></i>
                    <p class="text-sm text-gray-600">SẮP ĐẾN HẸN</p>
                    <p class="text-3xl font-bold text-600">
                        {{ $upcomingBookings }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 text-center border-2">
                    <i class="fas fa-wallet text-600 text-3xl mb-3"></i>
                    <p class="text-sm text-gray-600">CHI TIÊU</p>
                    <p class="text-3xl font-bold text-600">
                        {{ number_format($totalSpent) }} VNĐ
                    </p>
                </div>

            </div>

        </div>
    </div>

    <div id="profileModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl w-full max-w-2xl p-6 relative">

            <h2 class="text-xl font-bold mb-4">Cập nhật thông tin</h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="name"
                        value="{{ auth()->user()->name }}"
                        class="border p-2 rounded"
                        placeholder="Họ và tên">

                    <input type="text" name="phone"
                        value="{{ auth()->user()->phone }}"
                        class="border p-2 rounded"
                        placeholder="Số điện thoại">
                </div>

                <textarea name="bio"
                        class="border p-2 rounded w-full mt-4 h-32"
                        placeholder="Tiểu sử">{{ auth()->user()->bio }}</textarea>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">
                        Hủy
                    </button>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Lưu
                    </button>
                </div>

            </form>

            <form method="POST"
                action="{{ route('profile.password') }}"
                class="mt-6">

                @csrf
                @method('PUT')

                <hr class="my-6">

                <h3 class="text-lg font-semibold mb-4">
                    Đổi mật khẩu
                </h3>

                <div class="space-y-4">

                    <div>
                        <label class="block text-sm mb-1">
                            Mật khẩu hiện tại
                        </label>

                        <input
                            type="password"
                            name="current_password"
                            class="border p-2 rounded w-full"
                            placeholder="Nhập mật khẩu hiện tại"
                            required
                        >
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">
                            Mật khẩu mới
                        </label>

                        <input
                            type="password"
                            name="new_password"
                            class="border p-2 rounded w-full"
                            placeholder="Nhập mật khẩu mới"
                            required
                        >
                        @error('new_password')
                            <p class="text-red-500 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">
                            Xác nhận mật khẩu mới
                        </label>

                        <input
                            type="password"
                            name="new_password_confirmation"
                            class="border p-2 rounded w-full"
                            placeholder="Nhập lại mật khẩu mới"
                            required
                        >
                        </div>

                </div>

                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    >
                        Đổi mật khẩu
                    </button>
                </div>

            </form>

        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('profileModal').classList.remove('hidden');
            document.getElementById('profileModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('profileModal').classList.add('hidden');
            document.getElementById('profileModal').classList.remove('flex');
        }

        document.getElementById('profileModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
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

    @if ($errors->any() || session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                openModal();
            });
        </script>
    @endif
    @include('customer.footer')
</body>

</html>