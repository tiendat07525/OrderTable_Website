<nav class="fixed top-0 left-0 right-0 bg-slate-900/95 backdrop-blur-md z-50 border-b border-slate-800">
    <div class="w-full px-4">
        <div class="flex justify-between items-center h-24">
            <div class="flex items-center gap-6 ml-4">
                <a href="{{ route('customer.dashboard') }}"
                   class="text-4xl font-bold bg-gradient-to-r from-[#4647D3] to-[#8126CF] text-transparent bg-clip-text">
                    Golden Spoons
                </a>
            </div>

            <div class="flex items-center gap-6 mr-4">
                <a href="{{ route('customer.booking.index') }}"
                   class="{{ request()->routeIs('customer.booking.*') || request()->routeIs('customer.search') ? 'text-white' : 'text-slate-300' }} hover:text-white transition">
                    Đặt bàn
                </a>

                <a href="{{ route('customer.history') }}"
                   class="{{ request()->routeIs('customer.history') ? 'text-white' : 'text-slate-300' }} hover:text-white transition">
                    Lịch sử
                </a>

                <div class="relative" id="userMenuContainer">
                    <button id="userMenuButton" type="button"
                            class="p-2 hover:bg-slate-800 rounded-lg transition text-slate-400 hover:text-slate-200">
                        <i class="fas fa-user-circle text-3xl"></i>
                    </button>

                    <div id="userDropdown"
                         class="hidden absolute right-0 top-full mt-2 w-48 bg-slate-800 rounded-lg shadow-lg border border-slate-700 z-[9999]">
                        <div class="py-1 bg-slate-800 rounded-lg">
                            <a href="{{ route('customer.profile') }}"
                               class="block px-4 py-2 hover:bg-slate-700 transition text-slate-300 hover:text-slate-100">
                                Tài khoản
                            </a>

                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 hover:bg-slate-700 text-red-400 transition">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
