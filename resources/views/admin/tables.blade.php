<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bàn</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        @include('admin.sidebar')

        <main class="flex-1 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý bàn</h1>
                    <p class="text-sm text-gray-500 mt-1">Lọc bàn, xem booking theo ngày và quản lý sức chứa/khu vực.</p>
                </div>

                <button onclick="openModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Thêm bàn mới
                </button>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ $errors->first() }}</div>
            @endif

            <section class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Tổng bàn</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Trống</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['available'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Đặt trước</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['reserved'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Đang dùng</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['occupied'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Có booking hôm nay</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['booked_on_date'] }}</p>
                </div>
            </section>

            <section class="bg-white rounded-lg shadow-sm p-5 mb-6">
                <form method="GET" action="{{ route('admin.tables') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tìm kiếm</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                               placeholder="Tên bàn hoặc khu vực"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Khu vực</label>
                        <select name="location" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Tất cả</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" @selected(($filters['location'] ?? '') === $location)>{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Trạng thái</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Tất cả</option>
                            <option value="available" @selected(($filters['status'] ?? '') === 'available')>Trống</option>
                            <option value="reserved" @selected(($filters['status'] ?? '') === 'reserved')>Đã đặt</option>
                            <option value="occupied" @selected(($filters['status'] ?? '') === 'occupied')>Đã sử dụng</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sức chứa tối thiểu</label>
                        <input type="number" name="capacity_min" min="1" value="{{ $filters['capacity_min'] ?? '' }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ngày xem booking</label>
                        <input type="date" name="date" value="{{ $filters['date'] }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div class="md:col-span-6 flex gap-3">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Lọc bàn
                        </button>
                        <a href="{{ route('admin.tables') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Reset
                        </a>
                    </div>
                </form>
            </section>

            <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="p-3">Bàn</th>
                                <th class="p-3">Khu vực</th>
                                <th class="p-3">Sức chứa</th>
                                <th class="p-3">Giá cọc</th>
                                <th class="p-3">Trạng thái</th>
                                <th class="p-3">Booking</th>
                                <th class="p-3">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tables as $table)
                                <tr class="border-b hover:bg-gray-50 align-top">
                                    <td class="p-3">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $table->image ? asset('storage/' . $table->image) : asset('images/default.png') }}"
                                                 class="w-14 h-14 rounded-lg object-cover"
                                                 alt="">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $table->name }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ $table->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">{{ $table->location }}</td>
                                    <td class="p-3">{{ $table->capacity }} người</td>
                                    <td class="p-3">{{ number_format($table->price) }}đ</td>
                                    <td class="p-3">
                                        @if($table->status === 'available')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Trống</span>
                                        @elseif($table->status === 'reserved')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">Đặt trước</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">Đang dùng</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <div>{{ $table->today_bookings_count }} booking hôm nay</div>
                                        <div class="text-xs {{ $table->future_bookings_count > 0 ? 'text-orange-600 font-semibold' : 'text-gray-500' }}">
                                            {{ $table->future_bookings_count }} booking sắp tới
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <button onclick='openEditModal({{ $table->id }}, @json($table->name), @json($table->location), {{ $table->capacity }}, {{ $table->price }}, @json($table->status))'
                                                class="text-blue-600 hover:underline">
                                            Sửa
                                        </button>

                                        <form action="{{ route('admin.tables.destroy', $table->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Xác nhận xóa bàn này? Bàn có booking tương lai sẽ không thể xóa.');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ml-4">Xóa</button>
                                        </form>

                                        @if($table->status === 'reserved')
                                            <form action="{{ route('admin.tables.occupy', $table->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-orange-600 hover:underline ml-4">Đang dùng</button>
                                            </form>
                                        @elseif($table->status === 'occupied')
                                            <form action="{{ route('admin.tables.release', $table->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:underline ml-4">Giải phóng</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-gray-500">Không có bàn phù hợp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t">
                    {{ $tables->links() }}
                </div>
            </section>
        </main>
    </div>

    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white w-[520px] p-6 rounded-xl shadow-lg">
            <h2 class="text-xl font-bold mb-6 text-gray-800">Thêm bàn mới</h2>

            <form id="tableForm" action="{{ route('admin.tables.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-gray-700">Tên bàn</label>
                    <input name="name" id="name" class="w-full border rounded-lg p-3" required>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-gray-700">Khu vực</label>
                    <select name="location" id="location" class="w-full border rounded-lg p-3" required>
                        <option value="">-- Chọn khu vực --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-gray-700">Sức chứa</label>
                    <input type="number" name="capacity" id="capacity" min="1" class="w-full border rounded-lg p-3" required>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-gray-700">Giá cọc</label>
                    <input type="number" name="price" id="price" min="0" class="w-full border rounded-lg p-3" required>
                </div>

                <div class="mb-5">
                    <label class="block mb-1 font-medium text-gray-700">Ảnh bàn</label>
                    <input type="file" name="image" class="w-full border rounded-lg p-3">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Luu</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white w-[520px] p-6 rounded-xl shadow-lg">
            <h2 class="text-xl font-bold mb-6 text-gray-800">Chỉnh sửa bàn</h2>

            <form id="editTableForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-gray-700">Tên bàn</label>
                    <input name="name" id="edit_name" class="w-full border rounded-lg p-3">
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-gray-700">Khu vực</label>
                    <select name="location" id="edit_location" class="w-full border rounded-lg p-3">
                        @foreach($locations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-700">Sức chứa</label>
                        <input type="number" name="capacity" id="edit_capacity" min="1" class="w-full border rounded-lg p-3">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-700">Giá cọc</label>
                        <input type="number" name="price" id="edit_price" min="0" class="w-full border rounded-lg p-3">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-gray-700">Trạng thái</label>
                    <select name="status" id="edit_status" class="w-full border rounded-lg p-3">
                        <option value="available">Trống</option>
                        <option value="reserved">Đã đặt</option>
                        <option value="occupied">Đã sử dụng</option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block mb-1 font-medium text-gray-700">Ảnh bàn</label>
                    <input type="file" name="image" class="w-full border rounded-lg p-3">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            document.getElementById('modal').classList.remove('flex');
        }

        function openEditModal(id, name, location, capacity, price, status) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_location').value = location;
            document.getElementById('edit_capacity').value = capacity;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_status').value = status;
            document.getElementById('editTableForm').action = `/admin/tables/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
    </script>
</body>

</html>
