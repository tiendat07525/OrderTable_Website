<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Đăng nhập</h2>

        @if ($errors->has('login'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ $errors->first('login') }}
        </div>
        @endif

        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('auth.login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-700 font-medium mb-2">Tên đăng nhập</label>
                <input type="text" name="username" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300 focus:border-blue-500" required value="{{ old('username') }}">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Mật khẩu</label>

                <div class="relative">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full px-4 py-2 pr-12 border rounded-lg focus:ring focus:ring-blue-300 focus:border-blue-500"
                        required
                        value="{{ old('password') }}"
                    >

                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-500"
                    >
                        <ion-icon name="eye-outline" id="eyeIcon"></ion-icon>
                    </button>
                </div>
            </div>

            <a href="{{ route('forgot.password') }}"
                class="text-sm text-green-600 hover:underline">
                Quên mật khẩu?
            </a>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                Đăng nhập
            </button>
            <a href="{{ route('auth.google') }}"
            class="w-full flex items-center justify-center gap-3 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition font-medium mt-4">
                <ion-icon name="logo-google" class="text-xl"></ion-icon>
                Đăng nhập bằng Google
            </a>
        </form>
        <p class="text-center mt-4 text-gray-600">
            Chưa có tài khoản?
            <a href="{{ route('auth.register') }}" class="text-blue-600 hover:underline font-medium">Đăng ký</a>
        </p>
    </div>
    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const button = event.currentTarget;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            button.innerHTML = '<ion-icon name="eye-off-outline"></ion-icon>';
        } else {
            passwordInput.type = 'password';
            button.innerHTML = '<ion-icon name="eye-outline" id="eyeIcon"></ion-icon>';
        }
    }
</script>
</body>

</html>