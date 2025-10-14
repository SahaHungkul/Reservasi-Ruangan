<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string',
                'role' => 'nullable|string|in:admin,karyawan',
                'sort_by' => 'nullable|string|in:created_at,role,name',
                'sort_order' => 'nullable|string|in:asc,desc',
                'per_page' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if ($value === 'all') return; // valid

                        if (!ctype_digit(strval($value)) || (int)$value < 1) {
                            $fail("The $attribute field must be a positive integer or 'all'.");
                        }
                    },
                ],
                'page' => 'nullable|int|min:1',
            ]);

            $user = $this->userService->filterUser($validated);

           
            if ($validated['per_page'] ?? null === 'all') {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil memanggil semua data',
                    'pagination' => [
                        'per_page' => 'all',
                        'page' => '1/1',
                        'total' => $user->count(),
                    ],
                    'data' => UserResource::collection($user),
                ], 200);
            }

            // Jika pagination biasa
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memanggil data',
                'pagination' => [
                    'per_page' => $user->perPage(),
                    'page' => $user->currentPage() . '/' . $user->lastPage(),
                    'total' => $user->total(),
                ],
                'data' => $user->isEmpty() ? [null] : UserResource::collection($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memanggil data user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['password'] = bcrypt($data['password']);

            $user = User::create($data);

            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat user:',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            $data = $request->validated();

            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();
        return response()->json([
            'message' => 'User berhasil dihapus'
        ], 200);
    }
}
