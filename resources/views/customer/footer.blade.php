<footer class="bg-slate-900 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="font-bold text-white mb-4">Golden Spoons</h4>
                <p class="text-sm text-slate-400">© 2026 Golden Spoons. Technological Luxury.</p>
            </div>
            <div>
                <h4 class="font-semibold text-slate-300 mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="{{ route('customer.booking.index') }}" class="hover:text-violet-400 transition">Make Reservation</a></li>
                    <li><a href="{{ route('customer.dashboard') }}" class="hover:text-violet-400 transition">Search Tables</a></li>
                    <li><a href="{{ route('customer.history') }}" class="hover:text-violet-400 transition">My Bookings</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-slate-300 mb-4">Information</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="#" class="hover:text-violet-400 transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-violet-400 transition">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-violet-400 transition">Contact Support</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-slate-300 mb-4">Get In Touch</h4>
                <p class="text-sm text-slate-400 mb-2"><i class="fas fa-phone mr-2"></i>(555) 123-4567</p>
                <p class="text-sm text-slate-400"><i class="fas fa-envelope mr-2"></i>reservations@goldspoons.com</p>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 text-center text-sm text-slate-500">
            <p>&copy; 2026 Golden Spoons Restaurant. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const container = document.getElementById("userMenuContainer");
        const dropdown = document.getElementById("userDropdown");
        const button = document.getElementById("userMenuButton");

        if (!container || !dropdown || !button) return;

        let hideTimeout;

        const showDropdown = () => {
            clearTimeout(hideTimeout);
            dropdown.classList.remove("hidden");
        };

        const hideDropdown = () => {
            hideTimeout = setTimeout(() => dropdown.classList.add("hidden"), 120);
        };

        container.addEventListener("mouseenter", showDropdown);
        container.addEventListener("mouseleave", hideDropdown);
        dropdown.addEventListener("mouseenter", showDropdown);
        dropdown.addEventListener("mouseleave", hideDropdown);

        button.addEventListener("click", function (event) {
            event.stopPropagation();
            dropdown.classList.toggle("hidden");
        });

        document.addEventListener("click", function (event) {
            if (!container.contains(event.target)) {
                dropdown.classList.add("hidden");
            }
        });
    });
</script>
