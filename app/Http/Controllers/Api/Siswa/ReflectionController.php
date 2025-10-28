<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Reflection;
use App\Enums\Mood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReflectionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $date = $request->query('date');
        $month = $request->query('month');

        $reflections = Reflection::with(['category'])
            ->where('user_id', $user->id)
            ->when($date, function($query, $date) {
                return $query->whereDate('date', Carbon::parse($date));
            })
            ->when($month, function($query, $month) {
                $parsedMonth = Carbon::parse($month);
                return $query->whereYear('date', $parsedMonth->year)
                            ->whereMonth('date', $parsedMonth->month);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($reflection) {
                return [
                    'id' => $reflection->id,
                    'mood' => $reflection->mood->value,
                    'body' => $reflection->content,
                    'category' => optional($reflection->category)->name,
                    'category_id' => $reflection->category_id,
                    'is_private' => $reflection->is_private,
                    'date' => $reflection->date->format('Y-m-d'),
                    'created_at' => $reflection->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $reflection->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $reflections
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'mood' => 'required|in:happy,neutral,sad,angry,tired',
            'body' => 'required|string|min:10|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'is_private' => 'boolean',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $requestDate = Carbon::parse($request->date);
            $today = Carbon::today();

            // Cek jika tanggal di masa depan
            if ($requestDate->gt($today)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak dapat membuat refleksi untuk tanggal di masa depan'
                ], 400);
            }

            // HAPUS: Tidak perlu cek existing reflection untuk tanggal yang sama
            // User bisa membuat multiple reflections dalam sehari

            $reflection = Reflection::create([
                'user_id' => $user->id,
                'mood' => Mood::from($request->mood),
                'content' => $request->body,
                'category_id' => $request->category_id,
                'is_private' => $request->is_private ?? true,
                'date' => $requestDate,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Refleksi berhasil dibuat',
                'data' => [
                    'id' => $reflection->id,
                    'mood' => $reflection->mood->value,
                    'body' => $reflection->content,
                    'category' => optional($reflection->category)->name,
                    'is_private' => $reflection->is_private,
                    'date' => $reflection->date->format('Y-m-d'),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat refleksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $reflection = Reflection::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reflection) {
            return response()->json([
                'status' => 'error',
                'message' => 'Refleksi tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'mood' => 'sometimes|in:happy,neutral,sad,angry,tired',
            'body' => 'sometimes|string|min:10|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'is_private' => 'boolean',
            'date' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cek jika mengubah tanggal
            if ($request->has('date')) {
                $newDate = Carbon::parse($request->date);
                $today = Carbon::today();

                // Cek jika tanggal di masa depan
                if ($newDate->gt($today)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tidak dapat mengubah refleksi ke tanggal di masa depan'
                    ], 400);
                }

                $currentDate = $reflection->date;

                // HAPUS: Tidak perlu cek konflik tanggal karena bisa multiple reflections per hari
                // User bisa memiliki multiple reflections dalam sehari
            }

            $updateData = [];

            if ($request->has('mood')) {
                $updateData['mood'] = Mood::from($request->mood);
            }

            if ($request->has('body')) {
                $updateData['content'] = $request->body;
            }

            if ($request->has('category_id')) {
                $updateData['category_id'] = $request->category_id;
            }

            if ($request->has('is_private')) {
                $updateData['is_private'] = $request->is_private;
            }

            if ($request->has('date')) {
                $updateData['date'] = Carbon::parse($request->date);
            }

            $reflection->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Refleksi berhasil diupdate',
                'data' => [
                    'id' => $reflection->id,
                    'mood' => $reflection->mood->value,
                    'body' => $reflection->content,
                    'category' => optional($reflection->category)->name,
                    'is_private' => $reflection->is_private,
                    'date' => $reflection->date->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate refleksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $reflection = Reflection::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reflection) {
            return response()->json([
                'status' => 'error',
                'message' => 'Refleksi tidak ditemukan'
            ], 404);
        }

        try {
            $reflection->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Refleksi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus refleksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk today reflection - mengembalikan array karena bisa multiple
    public function today()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $reflections = Reflection::with(['category'])
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($reflection) {
                return [
                    'id' => $reflection->id,
                    'mood' => $reflection->mood->value,
                    'body' => $reflection->content,
                    'category' => optional($reflection->category)->name,
                    'category_id' => $reflection->category_id,
                    'is_private' => $reflection->is_private,
                    'date' => $reflection->date->format('Y-m-d'),
                    'created_at' => $reflection->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $reflection->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        // Kembalikan array kosong jika tidak ada refleksi hari ini
        if ($reflections->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => null
            ]);
        }

        // Kembalikan refleksi terbaru (yang pertama dalam array)
        return response()->json([
            'status' => 'success',
            'data' => $reflections->first()
        ]);
    }

    // Method baru untuk mendapatkan semua refleksi hari ini (bisa multiple)
    public function todayAll()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $reflections = Reflection::with(['category'])
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($reflection) {
                return [
                    'id' => $reflection->id,
                    'mood' => $reflection->mood->value,
                    'body' => $reflection->content,
                    'category' => optional($reflection->category)->name,
                    'category_id' => $reflection->category_id,
                    'is_private' => $reflection->is_private,
                    'date' => $reflection->date->format('Y-m-d'),
                    'created_at' => $reflection->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $reflection->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $reflections
        ]);
    }
}
