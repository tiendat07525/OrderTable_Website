<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Mail\PaymentSuccessMail;
use App\Models\Booking;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    public function index()
    {
        $tables = Table::paginate(9);

        return view('customer.listTable', compact('tables'));
    }

    public function adminIndex(Request $request)
    {
        $filters = $request->only([ //thêm filter
            'q',
            'status',
            'payment_status',
            'booking_date',
            'created_from',
            'created_to',
            'sort',
            'direction',
        ]);

        $query = Booking::with(['user', 'table', 'transactions', 'statusHistories.changedBy']);
        $this->applyAdminBookingFilters($query, $filters);

        $sort = in_array($filters['sort'] ?? '', ['id', 'date', 'time', 'created_at', 'status', 'payment_status', 'total_price'])
            ? $filters['sort']
            : 'created_at';
        $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $bookings = $query
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        $today = Carbon::today();
        $stats = [
            'today' => Booking::whereDate('date', $today)->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'paid_revenue' => Booking::where('payment_status', 'paid')->sum('total_price'),
            'upcoming' => Booking::whereIn('status', ['pending', 'confirmed'])
                ->whereDate('date', $today)
                ->whereTime('time', '>=', now()->format('H:i:s'))
                ->whereTime('time', '<=', now()->addHour()->format('H:i:s'))
                ->count(),
        ];

        return view('admin.bookings', compact('bookings', 'filters', 'stats'));
    }

    public function create($id)
    {
        $table = Table::findOrFail($id);

        if ($table->status !== 'available') {
            return response()->json(['error' => 'Bàn này đã có người đặt'], 400);
        }

        return view('customer.booking', compact('table'));
    }

    public function store(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'guest_count' => 'required|integer|min:1|max:' . $table->capacity,
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'special_requests' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|in:bank_transfer,cash',
        ]);

        $booking = DB::transaction(function () use ($request, $table) {
            $exists = Booking::where('table_id', $table->id)
                ->where('date', $request->booking_date)
                ->where('time', $request->booking_time)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                return null;
            }

            return Booking::create([
                'user_id' => Auth::id(),
                'table_id' => $table->id,
                'date' => $request->booking_date,
                'time' => $request->booking_time,
                'guest_count' => $request->guest_count,
                'email' => $request->email ?? Auth::user()->email,
                'phone' => $request->phone ?? Auth::user()->phone,
                'special_requests' => $request->special_requests,
                'total_price' => $table->price,
                'status' => 'pending',
                'payment_method' => $request->payment_method ?? 'bank_transfer',
            ]);
        });

        if (!$booking) {
            return redirect()->back()->with('error', 'Khung giờ này đã có người đặt');
        }

        return redirect()->route('customer.booking.confirm', $booking->id);
    }

    public function confirm($id)
    {
        $booking = Booking::with('table')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $paymentController = new PaymentController();
        $vietQrUrl = $paymentController->generateVietQr($booking);

        return view('customer.confirmBooking', compact('booking', 'vietQrUrl'));
    }

    public function confirmPayment($id)
    {
        $booking = Booking::with(['user', 'table'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($booking->payment_status === 'paid') {
            return response()->json(['success' => true]);
        }

        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'paid_at' => now(),
            'confirmed_at' => now(),
        ]);

        try {
            Mail::to($booking->user->email)
                ->queue(new PaymentSuccessMail($booking));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    public function checkStatus($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'paid' => $booking->payment_status === 'paid',
        ]);
    }

    public function history()
    {
        $user = Auth::user();
        $bookings = Booking::with('table')
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        return view('customer.history', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with('table')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('customer.detailBooking', compact('booking'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'guest_count' => 'nullable|integer|min:1',
            'location' => 'nullable|string',
        ]);

        if ($request->date && Carbon::parse($request->date)->lt(Carbon::today())) {
            return view('customer.listTable', [
                'tables' => collect(),
                'noResult' => true,
                'error' => 'Khong duoc chon ngay trong qua khu',
                'bookingTimes' => [],
            ]);
        }

        $query = Table::query()->where('status', 'available');

        if ($request->guest_count) {
            $query->where('capacity', '>=', $request->guest_count);
        }

        if ($request->location) {
            $query->where('location', $request->location);
        }

        $bookedTimes = [];
        if ($request->date) {
            $bookedTimes = Booking::where('date', $request->date)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->pluck('time')
                ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
                ->toArray();
        }

        $tables = $query->get();

        return view('customer.listTable', [
            'tables' => $tables,
            'noResult' => $tables->isEmpty(),
            'error' => $tables->isEmpty() ? 'Không tìm thấy bàn phù hợp với yêu cầu của bạn' : null,
            'bookingTimes' => $bookedTimes,
        ]);
    }

    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking đã bị hủy');
        }

        $bookingDateTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            Carbon::parse($booking->date)->format('Y-m-d') . ' ' . Carbon::parse($booking->time)->format('H:i:s')
        );

        if ($bookingDateTime->isPast()) {
            return back()->with('error', 'Không thể hủy booking đã diễn ra');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Hủy booking thành công');
    }

    public function getBookedTimes(Request $request)
    {
        if (!$request->table_id || !$request->date) {
            return response()->json([]);
        }

        $times = Booking::where('table_id', $request->table_id)
            ->where('date', $request->date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->pluck('time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
            ->toArray();

        return response()->json($times);
    }

    public function paymentStatus($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy booking',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'payment_status' => $booking->payment_status,
            'status' => $booking->status,
        ]);
    }

    private function applyAdminBookingFilters($query, array $filters): void
    {
        if (!empty($filters['q'])) {
            $keyword = trim($filters['q']);
            $bookingId = preg_replace('/\D+/', '', $keyword);

            $query->where(function ($subQuery) use ($keyword, $bookingId) {
                if ($bookingId !== '') {
                    $subQuery->orWhere('id', (int) $bookingId);
                }

                $subQuery
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery
                            ->where('username', 'like', "%{$keyword}%")
                            ->orWhere('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['booking_date'])) {
            $query->whereDate('date', $filters['booking_date']);
        }

        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
    }

    public function adminExport(Request $request)
    {
        $filters = $request->only([
            'q',
            'status',
            'payment_status',
            'booking_date',
            'created_from',
            'created_to',
        ]);

        $query = Booking::with(['user', 'table'])->latest();
        $this->applyAdminBookingFilters($query, $filters);

        $rows = [[
            'Mã booking',
            'Khách hàng',
            'Email',
            'Số điện thoại',
            'Bàn',
            'Ngày đặt',
            'Giờ đặt',
            'Số khách',
            'Trạng thái booking',
            'Trạng thái thanh toán',
            'Tổng tiền',
            'Ngày tạo',
        ]];

        foreach ($query->get() as $booking) {
            $rows[] = [
                '#' . $booking->id,
                $booking->user->username ?? $booking->user->name ?? '',
                $booking->email,
                $booking->phone,
                $booking->table->name ?? '',
                optional($booking->date)->format('d/m/Y'),
                Carbon::parse($booking->time)->format('H:i'),
                $booking->guest_count,
                $booking->status,
                $booking->payment_status,
                $booking->total_price,
                optional($booking->created_at)->format('d/m/Y H:i'),
            ];
        }

        return Excel::download(new ReportsExport($rows), 'bookings.xlsx');
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed,no_show',
            'note' => 'nullable|string|max:1000',
            'cancel_reason' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::with(['user', 'table'])->findOrFail($id);
        $oldStatus = $booking->status;
        $newStatus = $validated['status'];

        $updates = ['status' => $newStatus];

        if ($newStatus === 'confirmed' && !$booking->confirmed_at) {
            $updates['confirmed_at'] = now();
        }

        if ($newStatus === 'cancelled') {
            $updates['cancelled_at'] = now();
            $updates['cancel_reason'] = $validated['cancel_reason'] ?? $booking->cancel_reason;
        }

        if ($newStatus === 'completed' && !$booking->completed_at) {
            $updates['completed_at'] = now();
        }

        $booking->update($updates);
        $this->recordStatusHistory($booking, $oldStatus, $newStatus, $validated['note'] ?? null);

        return back()->with('success', 'Cập nhật trạng thái booking thành công');
    }

    public function adminResendEmail($id)
    {
        $booking = Booking::with(['user', 'table'])->findOrFail($id);

        if ($booking->payment_status !== 'paid') {
            return back()->with('error', 'Chỉ gửi lại email xác nhận cho booking đã thanh toán');
        }

        Mail::to($booking->email ?: $booking->user->email)
            ->queue(new PaymentSuccessMail($booking));

        return back()->with('success', 'Đã đưa email xác nhận vào hàng đợi gửi lại');
    }

    private function recordStatusHistory(Booking $booking, ?string $fromStatus, string $toStatus, ?string $note = null): void
    {
        if ($fromStatus === $toStatus && !$note) {
            return;
        }

        $booking->statusHistories()->create([
            'changed_by' => auth()->id(),
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => $note,
        ]);
    }
}
