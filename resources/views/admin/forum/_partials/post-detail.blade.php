@php
    $isAdminPost = $post->user && $post->user->role === 'admin';
@endphp

<div class="space-y-4">
    <!-- Post Header -->
    <div class="flex justify-between items-start gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-3">
                @if($post->is_pinned)
                <span class="text-yellow-600 bg-yellow-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-thumbtack"></i>
                    Pinned
                </span>
                @endif

                @if($isAdminPost)
                <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-crown text-yellow-500"></i>
                    Admin Post
                </span>
                @endif
            </div>

            <h1 class="text-2xl font-bold {{ $isAdminPost ? 'text-blue-900' : 'text-neutral-900' }} mb-4">
                {{ $post->title }}
            </h1>

            <!-- Author & Metadata -->
            <div class="flex items-center gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 {{ $isAdminPost ? 'bg-blue-100 border-2 border-blue-300' : 'bg-neutral-100' }} rounded-full flex items-center justify-center">
                        <i class="fas fa-user {{ $isAdminPost ? 'text-blue-600' : 'text-neutral-500' }}"></i>
                    </div>
                    <div>
                        <div class="font-semibold {{ $isAdminPost ? 'text-blue-800' : 'text-neutral-700' }}">
                            {{ $post->user->name ?? '—' }}
                            @if($isAdminPost)
                            <span class="text-blue-600 text-sm ml-2">(Administrator)</span>
                            @endif
                        </div>
                        <div class="text-sm text-neutral-500">
                            {{ $post->created_at ? $post->created_at->format('d M Y H:i') : "-" }}
                        </div>
                    </div>
                </div>

                <div class="w-px h-8 bg-neutral-300"></div>

                <div class="flex items-center gap-4 text-sm text-neutral-600">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-comments"></i>
                        {{ $post->comments()->count() }} komentar
                    </span>
                    <span class="flex items-center gap-2">
                        <i class="fas fa-layer-group"></i>
                        @if($post->scope_type === 'class' && $post->classRoom)
                        Kelas: {{ $post->classRoom->name }}
                        @else
                        Global
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Post Actions -->
        <div class="flex flex-col gap-2">
            <!-- Lock/Unlock -->
            @if($post->is_locked)
            <form action="{{ route('admin.forum.unlock', $post) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="w-10 h-10 flex items-center justify-center bg-green-100 text-green-700 hover:bg-green-200 rounded-lg transition-colors" title="Unlock">
                    <i class="fas fa-lock-open"></i>
                </button>
            </form>
            @else
            <form action="{{ route('admin.forum.lock', $post) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="w-10 h-10 flex items-center justify-center bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition-colors" title="Lock">
                    <i class="fas fa-lock"></i>
                </button>
            </form>
            @endif

            <!-- Pin (toggle) -->
            <form action="{{ route('admin.forum.toggle-pin', $post) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="w-10 h-10 flex items-center justify-center {{ $post->is_pinned ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-lg transition-colors"
                    title="{{ $post->is_pinned ? 'Unpin' : 'Pin' }}">
                    <i class="fas fa-thumbtack"></i>
                </button>
            </form>

            <!-- Delete -->
            <form action="{{ route('admin.forum.destroy', $post) }}" method="POST" class="inline"
                onsubmit="return confirm('Hapus postingan ini? Semua komentar juga akan terhapus.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-10 h-10 flex items-center justify-center bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition-colors" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Post Content -->
    <div class="prose max-w-none border-t border-neutral-200 pt-6">
        <div class="text-neutral-700 leading-relaxed">
            {!! $post->content !!}
        </div>
    </div>
</div>
