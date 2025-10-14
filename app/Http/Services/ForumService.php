<?php

namespace App\Http\Services;

use App\Models\ForumPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ForumService
{
    public function getFilteredPosts(array $filters = [])
    {
        $query = ForumPost::with(['user', 'classRoom:id,name']);

        // Filter scope
        if (isset($filters['scope'])) {
            if ($filters['scope'] === 'global') {
                $query->where('scope_type', null)->orWhere('scope_type', 'global');
            } elseif ($filters['scope'] === 'class') {
                $query->where('scope_type', 'class');
            }
        }

        // Search: judul atau nama user
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Urutkan: pinned dulu, lalu terbaru
        $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');

        return $query->paginate(10);
    }
}
