@use('Illuminate\Support\Str')
@php
    $isAdminPost = $post->user && $post->user->role === 'admin';
@endphp

<div class="bg-white rounded-xl border-2 {{ $isAdminPost ? 'border-blue-200 shadow-lg admin-post-glow' : 'border-neutral-200 shadow-sm' }} p-6 transition-all duration-300 hover:shadow-xl relative overflow-hidden {{ $isAdminPost ? 'admin-post-animation' : '' }}">

    <!-- Premium Header untuk Admin -->
    @if($isAdminPost)
    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>
    <div class="absolute top-3 right-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-3 py-1.5 text-xs font-bold rounded-full shadow-lg flex items-center gap-2 z-10">
        <i class="fas fa-shield-alt text-yellow-300"></i>
        Official Announcement
    </div>
    @endif

    <div class="flex justify-between items-start gap-4 {{ $isAdminPost ? 'mt-2' : '' }}">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-3">
                @if($post->is_pinned)
                <span class="text-yellow-600 bg-yellow-100 border border-yellow-200 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1" title="Pinned">
                    <i class="fas fa-thumbtack text-xs"></i> Pinned
                </span>
                @endif

                <h3 class="text-xl font-bold {{ $isAdminPost ? 'text-blue-900 bg-gradient-to-r from-blue-900 to-indigo-900 bg-clip-text text-transparent' : 'text-neutral-900' }} flex-1">
                    <a href="{{ route('admin.forum.show', parameters: $post) }}" class="hover:text-blue-700 transition-colors flex items-center gap-3">
                        @if($isAdminPost)
                        <span class="w-2 h-8 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></span>
                        @endif
                        {{ $post->title }}
                        @if($isAdminPost)
                        <span class="text-blue-500 text-lg">
                            <i class="fas fa-badge-check"></i>
                        </span>
                        @endif
                    </a>
                </h3>
            </div>

            <!-- Author Badge untuk Admin -->
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center gap-2 {{ $isAdminPost ? 'bg-blue-50 border border-blue-200' : 'bg-neutral-50' }} px-3 py-2 rounded-lg">
                    <div class="w-8 h-8 {{ $isAdminPost ? 'bg-blue-100 border-2 border-blue-300' : 'bg-neutral-100' }} rounded-full flex items-center justify-center">
                        <i class="fas fa-user {{ $isAdminPost ? 'text-blue-600' : 'text-neutral-500' }} text-sm"></i>
                    </div>
                    <div>
                        <div class="font-semibold {{ $isAdminPost ? 'text-blue-800' : 'text-neutral-700' }} text-sm">
                            {{ $post->user->name ?? '—' }}
                        </div>
                        <div class="text-xs {{ $isAdminPost ? 'text-blue-600' : 'text-neutral-500' }}">
                            {{ $isAdminPost ? 'Administrator' : 'User' }}
                        </div>
                    </div>
                </div>

                @if($post->is_locked)
                <span class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                    <i class="fas fa-lock mr-1.5 text-xs"></i> Locked
                </span>
                @endif
            </div>

            <p class="text-sm {{ $isAdminPost ? 'text-blue-800 bg-blue-50 px-4 py-3 rounded-lg border border-blue-100' : 'text-neutral-600' }} line-clamp-2 mb-4">
                <i class="fas fa-quote-left {{ $isAdminPost ? 'text-blue-400 mr-2' : 'text-neutral-400 mr-1' }}"></i>
                {!! Str::limit(strip_tags($post->content), 120) !!}
            </p>

            <div class="flex flex-wrap items-center text-xs {{ $isAdminPost ? 'text-blue-700' : 'text-neutral-500' }} gap-3">
                <span class="flex items-center gap-2 {{ $isAdminPost ? 'bg-blue-100 border border-blue-200' : 'bg-neutral-50' }} px-3 py-2 rounded-lg">
                    <i class="fas fa-calendar {{ $isAdminPost ? 'text-blue-600' : '' }}"></i>
                    {{ $post->created_at->format('d M Y H:i') }}
                </span>
                <span class="flex items-center gap-2 {{ $isAdminPost ? 'bg-blue-100 border border-blue-200' : 'bg-neutral-50' }} px-3 py-2 rounded-lg">
                    <i class="fas fa-comments {{ $isAdminPost ? 'text-blue-600' : '' }}"></i>
                    {{ $post->comments()->count() }} komentar
                </span>
                <span class="flex items-center gap-2 {{ $isAdminPost ? 'bg-blue-100 border border-blue-200' : 'bg-neutral-50' }} px-3 py-2 rounded-lg">
                    <i class="fas fa-layer-group {{ $isAdminPost ? 'text-blue-600' : '' }}"></i>
                    @if($post->scope_type === 'class' && $post->classRoom)
                    Kelas: {{ $post->classRoom->name }}
                    @else
                    Global
                    @endif
                </span>
            </div>
        </div>

        <!-- Action Buttons dengan Desain Premium -->
        <div class="flex flex-col gap-3 {{ $isAdminPost ? 'bg-blue-50 p-3 rounded-xl border border-blue-200' : '' }}">
            <div class="text-xs font-semibold {{ $isAdminPost ? 'text-blue-800 text-center' : 'text-neutral-500' }} mb-1">
                Actions
            </div>

            <!-- Lock/Unlock -->
            @if($post->is_locked)
            <form action="{{ route('admin.forum.unlock', $post) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="w-10 h-10 flex items-center justify-center {{ $isAdminPost ? 'bg-green-100 border border-green-300 text-green-700 hover:bg-green-200' : 'bg-green-50 text-green-600 hover:bg-green-100' }} rounded-xl transition-all duration-200 hover:scale-105" title="Unlock">
                    <i class="fas fa-lock-open"></i>
                </button>
            </form>
            @else
            <form action="{{ route('admin.forum.lock', $post) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="w-10 h-10 flex items-center justify-center {{ $isAdminPost ? 'bg-red-100 border border-red-300 text-red-700 hover:bg-red-200' : 'bg-red-50 text-red-600 hover:bg-red-100' }} rounded-xl transition-all duration-200 hover:scale-105" title="Lock">
                    <i class="fas fa-lock"></i>
                </button>
            </form>
            @endif

            <!-- Pin (toggle) -->
            <form action="{{ route('admin.forum.toggle-pin', $post) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="w-10 h-10 flex items-center justify-center {{ $post->is_pinned ? ($isAdminPost ? 'bg-yellow-100 border border-yellow-300 text-yellow-700 hover:bg-yellow-200' : 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100') : ($isAdminPost ? 'bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100') }} rounded-xl transition-all duration-200 hover:scale-105"
                    title="{{ $post->is_pinned ? 'Unpin' : 'Pin' }}">
                    <i class="fas fa-thumbtack"></i>
                </button>
            </form>

            <!-- View Detail -->
            <a href="{{ route('admin.forum.show', $post) }}" class="w-10 h-10 flex items-center justify-center {{ $isAdminPost ? 'bg-blue-100 border border-blue-300 text-blue-700 hover:bg-blue-200' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }} rounded-xl transition-all duration-200 hover:scale-105"
                title="Lihat detail">
                <i class="fas fa-eye"></i>
            </a>

            <!-- Delete -->
            <form action="{{ route('admin.forum.destroy', $post) }}" method="POST" class="inline"
                onsubmit="return confirm('Hapus postingan ini? Semua komentar juga akan terhapus.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-10 h-10 flex items-center justify-center {{ $isAdminPost ? 'bg-red-100 border border-red-300 text-red-700 hover:bg-red-200' : 'bg-red-50 text-red-500 hover:bg-red-100' }} rounded-xl transition-all duration-200 hover:scale-105" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
