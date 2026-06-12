<aside class="w-64 bg-gray-900 text-white min-h-screen p-6">
    <div class="mb-8">
        <h2 class="text-xl font-bold">Quản Lý nhà hàng</h2>
    </div>

    <nav class="space-y-3">
        <a href="{{ route('admin.dashboard') }}"
            class="block px-4 py-2 rounded-lg transition font-medium
            {{ request()->routeIs('admin.dashboard*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            Bảng điều khiển
        </a>

        <a href="{{ route('admin.bookings') }}"
            class="block px-4 py-2 rounded-lg transition font-medium
            {{ request()->routeIs('admin.bookings*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            Quản lý đặt bàn
        </a>

        <a href="{{ route('admin.tables') }}"
            class="block px-4 py-2 rounded-lg transition font-medium
            {{ request()->routeIs('admin.tables*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            Quản lý bàn
        </a>

        <a href="{{ route('admin.revenue') }}"
            class="block px-4 py-2 rounded-lg transition font-medium
            {{ request()->routeIs('admin.revenue*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            Doanh thu
        </a>

        <a href="{{ route('admin.reports') }}"
            class="block px-4 py-2 rounded-lg transition font-medium
            {{ request()->routeIs('admin.reports*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            Báo cáo
        </a>
    </nav>
</aside>