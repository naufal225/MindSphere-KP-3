<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumCommentController extends Controller
{
    public function store(Request $request, ForumPost $post)
    {
        // Tentukan field name berdasarkan parent_id
        $fieldName = $request->parent_id ? "content_{$request->parent_id}" : 'content_main';

        // Validasi
        $request->validate([
            $fieldName => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:forum_comments,id,post_id,' . $post->id,
        ], [
            "{$fieldName}.required" => 'Komentar tidak boleh kosong.',
            "{$fieldName}.max" => 'Komentar maksimal 5000 karakter.',
            'parent_id.exists' => 'Balasan ke komentar yang tidak valid.',
        ]);

        if ($post->is_locked) {
            return back()->withErrors(['Komentar gagal ditambahkan. Postingan ini dikunci.']);
        }

        $comment = new ForumComment();
        $comment->post_id = $post->id;
        $comment->user_id = Auth::id();
        $comment->content = $request->$fieldName; // Ambil dari field yang benar
        $comment->parent_id = $request->parent_id;
        $comment->save();

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function destroy(ForumPost $post, ForumComment $comment)
    {
        // ✅ Pastikan komentar ini benar-benar milik post ini
        if ($comment->post_id !== $post->id) {
            abort(404, 'Komentar tidak ditemukan di postingan ini.');
        }

        // Hapus komentar (dan semua balasannya secara rekursif jika diperlukan)
        // Jika kamu ingin hapus rekursif, gunakan observer atau method khusus.
        // Untuk sekarang, kita hapus manual semua children.
        $this->deleteCommentAndReplies($comment);

        return back()->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Hapus komentar beserta semua balasannya (rekursif).
     */
    private function deleteCommentAndReplies(ForumComment $comment)
    {
        // Hapus semua balasan anaknya terlebih dahulu
        foreach ($comment->children as $child) {
            $this->deleteCommentAndReplies($child);
        }

        // Hapus lampiran jika ada
        $comment->attachments()->delete();

        // Hapus komentar utama
        $comment->delete();
    }
}
