<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        try {
            $categories = Category::orderBy('name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for specific type (habits, challenges, etc)
     */
    public function getByType(Request $request, $type)
    {
        try {
            $validTypes = ['habits', 'challenges', 'reflections', 'badges', 'posts'];

            if (!in_array($type, $validTypes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tipe kategori tidak valid'
                ], 400);
            }

            $categories = Category::whereHas($type)->orderBy('name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories with counts
     */
    public function getWithCounts()
    {
        try {
            $categories = Category::withCount(['habits', 'challenges', 'reflections', 'badges'])
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for challenges specifically
     */
    public function getForChallenges()
    {
        try {
            $categories = Category::whereHas('challenges')
                ->orWhere('code', 'like', '%challenge%')
                ->orderBy('name')
                ->get();

            // Jika tidak ada kategori khusus challenges, return semua kategori
            if ($categories->isEmpty()) {
                $categories = Category::orderBy('name')->get();
            }

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat kategori untuk challenges',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for habits specifically
     */
    public function getForHabits()
    {
        try {
            $categories = Category::whereHas('habits')
                ->orWhere('code', 'like', '%habit%')
                ->orderBy('name')
                ->get();

            // Jika tidak ada kategori khusus habits, return semua kategori
            if ($categories->isEmpty()) {
                $categories = Category::orderBy('name')->get();
            }

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat kategori untuk habits',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
