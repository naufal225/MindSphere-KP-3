@extends('components.admin.layout.app')
@section('header', 'Detail Forum')
@section('subtitle', 'Kelola postingan dan komentar forum')

@section('content')
<main class="relative z-10 flex-1 p-0 space-y-6 overflow-x-hidden overflow-y-auto bg-gray-50">
    <!-- Header & Navigation -->
    <div class="px-6 pt-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.forum.index') }}" class="flex items-center gap-2 text-blue-600 hover:text-blue-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Forum</span>
                </a>
                <div class="w-px h-6 bg-neutral-300"></div>
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900">Detail Postingan</h1>
                    <p class="text-neutral-600">Kelola postingan dan komentar</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mx-6 flex items-center p-4 border border-green-200 bg-green-50 rounded-xl">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="px-6 space-y-6">
        <!-- Post Detail Card -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">
            @include('admin.forum._partials.post-detail', ['post' => $post])
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                    <i class="fas fa-comments text-blue-600"></i>
                    Komentar
                    <span class="bg-blue-100 text-blue-800 text-sm px-2.5 py-0.5 rounded-full">
                        {{ $post->comments()->count() }}
                    </span>
                </h3>

                <!-- Comment Actions -->
                <div class="flex items-center gap-3">
                    @if($post->is_locked)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-lock mr-1.5"></i> Locked
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-lock-open mr-1.5"></i> Open
                    </span>
                    @endif
                </div>
            </div>

            <!-- Add Comment Form -->
            @if(!$post->is_locked)
                @include('admin.forum._partials.comment-form', [
                    'post' => $post,
                    'parentId' => null
                ])
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <i class="fas fa-lock text-yellow-500 text-lg mb-2"></i>
                    <p class="text-yellow-800 font-medium">Postingan ini dikunci. Tidak dapat menambah komentar baru.</p>
                </div>
            @endif

            <!-- Comments List -->
            <div class="mt-8 space-y-4" id="comments-container">
                @foreach($post->comments()->latest()->whereNull('parent_id')->get() as $comment)
                    @include('admin.forum._partials.comment-item', [
                        'comment' => $comment,
                        'depth' => 0
                    ])
                @endforeach

                @if($post->comments()->count() === 0)
                    <div class="text-center py-8">
                        <i class="fas fa-comment-slash text-3xl text-neutral-300 mb-3"></i>
                        <p class="text-neutral-500">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>

<!-- JavaScript untuk komentar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle reply form
    window.toggleReplyForm = function(commentId) {
        const form = document.getElementById(`reply-form-${commentId}`);
        const button = document.getElementById(`reply-button-${commentId}`);

        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
            button.innerHTML = '<i class="fas fa-times mr-2"></i>Batal';
        } else {
            form.classList.add('hidden');
            button.innerHTML = '<i class="fas fa-reply mr-2"></i>Balas';
        }
    };

    // Toggle nested comments
    window.toggleNestedComments = function(commentId) {
        const container = document.getElementById(`nested-comments-${commentId}`);
        const button = document.getElementById(`toggle-nested-${commentId}`);
        const icon = document.getElementById(`toggle-icon-${commentId}`);

        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            button.textContent = 'Sembunyikan balasan';
            icon.className = 'fas fa-chevron-up text-xs';
        } else {
            container.classList.add('hidden');
            button.textContent = 'Lihat balasan';
            icon.className = 'fas fa-chevron-down text-xs';
        }
    };

    // Auto-resize textarea
    document.querySelectorAll('.comment-textarea').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});
</script>

<style>
.comment-textarea {
    resize: none;
    min-height: 40px;
    max-height: 200px;
}

.nested-comment {
    border-left: 3px solid #e5e7eb;
    transition: border-color 0.2s ease;
}

.nested-comment:hover {
    border-left-color: #3b82f6;
}

.expand-transition {
    transition: all 0.3s ease-in-out;
}
</style>
@endsection
