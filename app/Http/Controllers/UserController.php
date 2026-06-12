<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() //done
    {
        return response()->json([
            'data' => User::all(),
            'message' => 'API người dùng hoạt động'
            ], 200);
    }

    public function show($id) //done
    {
        $user = User::findOrFail($id);

        return response()->json([
            'message' => 'Lấy thông tin cá nhân thành công',
            'data' => $user
        ], 200);
    }

    public function updateProfile(Request $request) //done
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại'], 404);
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20'
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Cập nhật thông tin cá nhân thành công',
            'data' => $user
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại'], 404);
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:current_password'
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Mật khẩu hiện tại không đúng'], 401);
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        return response()->json(['message' => 'Thay đổi mật khẩu thành công'], 200);
    }

    public function destroy($id)
    {
        User::destroy($id);

        return response()->json(['message' => 'Xóa người dùng thành công'], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:15',
            'bio' => 'sometimes|nullable|string|max:1000',
            'password' => 'sometimes|required|string|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Cập nhật người dùng thành công',
            'data' => $user
        ], 200);
    }
}
