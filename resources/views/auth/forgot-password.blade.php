<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="w-full max-w-md bg-white p-8 rounded-lg shadow">

    <h2 class="text-2xl font-bold text-center mb-6">
        Quên mật khẩu
    </h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('forgot.password.post') }}"
          class="space-y-4">

        @csrf

        <div>
            <label>Email</label>

            <input
                type="email"
                name="email"
                class="w-full border rounded-lg px-4 py-2"
                required
            >

            @error('email')
                <p class="text-red-500 text-sm">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-green-600 text-white py-2 rounded-lg"
        >
            Gửi mật khẩu mới
        </button>

        <div>
            <a href="{{ route('auth.login') }}" class="text-blue-600 hover:underline">
                Quay lại đăng nhập
            </a>
        </div>

    </form>

</div>

</body>
</html>