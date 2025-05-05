<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return UserModel::with('level')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:m_user,username',
            'nama' => 'required',
            'password' => 'required|min:6',
            'level_id' => 'required|exists:m_level,level_id'
        ]);

        $user = UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id
        ]);

        return response()->json($user, 201);
    }

    public function show(UserModel $user)
    {
        return response()->json($user->load('level'));
    }

    public function update(Request $request, UserModel $user)
    {
        $request->validate([
            'username' => 'sometimes|unique:m_user,username,' . $user->user_id . ',user_id',
            'nama' => 'sometimes',
            'password' => 'sometimes|min:6',
            'level_id' => 'sometimes|exists:m_level,level_id'
        ]);

        $data = $request->all();

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return UserModel::with('level')->find($user->user_id);
    }

    public function destroy(UserModel $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil dihapus'
        ]);
    }
}
