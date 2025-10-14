<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\ForumService;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    protected $forumService;

    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }

    public function index(Request $request)
    {
        $filters = [
            'scope' => $request->query('scope'),
            'search' => $request->query('search'),
        ];

        $forums = $this->forumService->getFilteredPosts($filters);

        return view('admin.forum.index', compact('forums'));
    }

    // Tambahkan di ForumController

    public function lock(ForumPost $post)
    {
        $post->update(['is_locked' => true, 'locked_at' => now()]);
        return back()->with('success', 'Postingan berhasil dikunci.');
    }

    public function unlock(ForumPost $post)
    {
        $post->update(['is_locked' => false, 'locked_at' => null]);
        return back()->with('success', 'Postingan berhasil dibuka.');
    }

    public function togglePin(ForumPost $post)
    {
        $post->update(['is_pinned' => !$post->is_pinned]);
        return back()->with('success', $post->is_pinned ? 'Postingan dipin.' : 'Postingan tidak dipin.');
    }

    public function destroy(ForumPost $post)
    {
        $post->delete();
        return back()->with('success', 'Postingan berhasil dihapus.');
    }

    public function show(ForumPost $post)
    {
        // Load relasi lengkap untuk detail
        $post->load([
            'user',
            'classRoom',
            'attachments',
            'comments.user',
            'comments.replies.user',
            'comments.replies.replies.user',
            'comments.replies.replies.replies.user',
        ]);
        return view('admin.forum.show', compact('post'));
    }
}
