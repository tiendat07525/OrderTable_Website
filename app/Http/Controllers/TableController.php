<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    private array $locations = [
        'Sảnh chính',
        'Sân thượng',
        'Khu VIP',
    ];

    public function adminIndex(Request $request)
    {
        $filters = $request->only(['q', 'location', 'status', 'capacity_min', 'date']);
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();

        $query = Table::query()
            ->withCount([
                'bookings as today_bookings_count' => function ($bookingQuery) use ($date) {
                    $bookingQuery
                        ->whereDate('date', $date)
                        ->whereIn('status', ['pending', 'confirmed']);
                },
                'bookings as future_bookings_count' => function ($bookingQuery) {
                    $bookingQuery
                        ->whereDate('date', '>=', Carbon::today())
                        ->whereIn('status', ['pending', 'confirmed']);
                },
            ]);

        if (!empty($filters['q'])) {
            $keyword = trim($filters['q']);
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery
                    ->where('name', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['capacity_min'])) {
            $query->where('capacity', '>=', (int) $filters['capacity_min']);
        }

        $tables = $query
            ->orderBy('location')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Table::count(),
            'available' => Table::where('status', 'available')->count(),
            'reserved' => Table::where('status', 'reserved')->count(),
            'occupied' => Table::where('status', 'occupied')->count(),
            'booked_on_date' => Booking::whereDate('date', $date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->distinct('table_id')
                ->count('table_id'),
        ];

        $locations = $this->locations;
        $filters['date'] = $date->format('Y-m-d');

        return view('admin.tables', compact('tables', 'filters', 'stats', 'locations'));
    }

    public function show($id)
    {
        $table = Table::findOrFail($id);

        return response()->json($table, 200);
    }

    public function create()
    {
        return redirect()->route('admin.tables');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tables',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'location' => ['required', Rule::in($this->locations)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tables', 'public');
        }

        Table::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'price' => (int) $request->price,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.tables')->with('success', 'Thêm bàn mới thành công');
    }

    public function edit($id)
    {
        Table::findOrFail($id);

        return redirect()->route('admin.tables');
    }

    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:tables,name,' . $id,
            'capacity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:available,reserved,occupied',
            'price' => 'sometimes|numeric|min:0',
            'location' => ['sometimes', Rule::in($this->locations)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($table->image) {
                Storage::disk('public')->delete($table->image);
            }

            $validated['image'] = $request->file('image')->store('tables', 'public');
        }

        $table->update($validated);

        return redirect()->route('admin.tables')->with('success', 'Cập nhật bàn thành công');
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);

        $hasFutureBookings = $table->bookings()
            ->whereDate('date', '>=', Carbon::today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasFutureBookings) {
            return redirect()->route('admin.tables')
                ->with('error', 'Không thể xóa bàn đang có booking');
        }

        if ($table->image) {
            Storage::disk('public')->delete($table->image);
        }

        $table->delete();

        return redirect()->route('admin.tables')->with('success', 'Xóa bàn thành công');
    }

    public function occupy($id)
    {
        $table = Table::findOrFail($id);

        if ($table->status !== 'reserved') {
            return redirect()->route('admin.tables')->with('error', 'Bàn phải được đặt trước khi chuyển sang đang dùng');
        }

        $table->update(['status' => 'occupied']);

        return redirect()->route('admin.tables')->with('success', 'Bàn đang được sử dụng');
    }

    public function release($id)
    {
        $table = Table::findOrFail($id);
        $table->update(['status' => 'available']);

        return redirect()->route('admin.tables')->with('success', 'Bàn đã trống');
    }
}
