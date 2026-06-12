<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Đăng ký</h2>
        <form method="POST" action="{{ route('auth.register.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-gray-700">Tên đăng nhập</label>
                <input
                    type="text"
                    name="username"
                    class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                    required>
            </div>

            <div>
                <label class="block text-gray-700">Email</label>

                <div class="flex gap-2">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                        required>

                    <button
                        type="button"
                        onclick="sendOtp()"
                        class="bg-blue-600 text-white px-4 rounded-lg hover:bg-blue-700 whitespace-nowrap">
                        Gửi OTP
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-gray-700">Mã OTP</label>

                <input
                    type="text"
                    name="otp"
                    maxlength="6"
                    placeholder="Nhập mã OTP"
                    class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300 tracking-[0.3em]"
                    required>
            </div>

            <div>
                <label class="block text-gray-700">Mật khẩu</label>

                <div class="relative">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full px-4 py-2 pr-12 border rounded-lg focus:ring focus:ring-green-300"
                        required>

                    <button
                        type="button"
                        onclick="togglePassword('password', 'eyeIcon1')"
                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-500">
                        <ion-icon name="eye-outline" id="eyeIcon1"></ion-icon>
                    </button>
                </div>

                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700">Xác nhận mật khẩu</label>

                <div class="relative">
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="w-full px-4 py-2 pr-12 border rounded-lg focus:ring focus:ring-green-300"
                        required>

                    <button
                        type="button"
                        onclick="togglePassword('password_confirmation', 'eyeIcon2')"
                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-500">
                        <ion-icon name="eye-outline" id="eyeIcon2"></ion-icon>
                    </button>
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                Đăng ký
            </button>
        </form>
        <p class="text-center mt-4 text-gray-600">
            Đã có tài khoản?
            <a href="{{ route('auth.login') }}" class="text-green-600 hover:underline">Đăng nhập</a>
        </p>
    </div>
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('name', 'eye-off-outline');
            } else {
                input.type = 'password';
                icon.setAttribute('name', 'eye-outline');
            }
        }
        async function sendOtp() {

        const email = document.getElementById('email').value;

        if (!email) {
            alert('Vui lòng nhập email');
            return;
        }

        try {

            const response = await fetch('/send-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: email
                })
            });

            const data = await response.json();

            alert(data.message);

        } catch (error) {

            alert('Có lỗi xảy ra');
        }
    }
    </script>
</body>
</html>