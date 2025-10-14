@php
$isAdminComment = $comment->user && $comment->user->role === 'admin';
$hasReplies = $comment->replies->count() > 0;
$marginLeft = $depth * 20;
$totalReplies = $comment->total_replies_count;
@endphp

<div class="nested-comment pl-6 expand-transition" style="margin-left: {{ $marginLeft }}px">
    <div class="bg-white border border-neutral-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
        <!-- Comment Header -->
        <div class="flex justify-between items-start gap-4 mb-3">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 {{ $isAdminComment ? 'bg-blue-100 border border-blue-300' : 'bg-neutral-100' }} rounded-full flex items-center justify-center">
                    <i class="fas fa-user {{ $isAdminComment ? 'text-blue-600' : 'text-neutral-500' }} text-sm"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="font-semibold {{ $isAdminComment ? 'text-blue-800' : 'text-neutral-700' }} text-sm">
                            {{ $comment->user->name ?? '—' }}
                        </span>
                        @if($isAdminComment)
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full font-medium">
                            Admin
                        </span>
                        @endif
                    </div>
                    <div class="text-xs text-neutral-500">
                        {{ $comment->created_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Comment Actions -->
            <div class="flex items-center gap-1">
                @if(!$post->is_locked)
                <button type="button" onclick="toggleReplyForm({{ $comment->id }})" id="reply-button-{{ $comment->id }}"
                    class="flex items-center gap-1 px-3 py-1.5 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                    <i class="fas fa-reply"></i>
                    Balas
                </button>
                @endif

                @if(auth()->user()->role === 'admin')
                <form action="{{ route('admin.forum.comments.destroy', [$post->id, $comment->id]) }}" method="POST"
                    onsubmit="return confirm('Hapus komentar ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center gap-1 px-3 py-1.5 text-xs text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Comment Content -->
        <div class="text-neutral-700 text-sm leading-relaxed mb-3">
            {!! nl2br(e($comment->content)) !!}
        </div>

        <!-- Reply Form (Hidden by Default) -->
        <div id="reply-form-{{ $comment->id }}" class="hidden">
            @include('admin.forum._partials.comment-form', [
            'post' => $post,
            'parentId' => $comment->id
            ])
        </div>

        <!-- Nested Comments -->
        @if($hasReplies)
        <div class="mt-3">
            <!-- Toggle Button for Nested Comments -->
            <button type="button" onclick="toggleNestedComments({{ $comment->id }})"
                id="toggle-nested-{{ $comment->id }}"
                class="flex items-center gap-2 text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                <i id="toggle-icon-{{ $comment->id }}" class="fas fa-chevron-down text-xs"></i>
                <span>
                    {{ $totalReplies }}
                    balasan
                </span>
            </button>

            <!-- Nested Comments Container (Hidden by Default) -->
            <div id="nested-comments-{{ $comment->id }}" class="hidden expand-transition">
                <div class="mt-3 space-y-3">
                    @foreach($comment->replies as $reply)
                    @include('admin.forum._partials.comment-item', [
                    'comment' => $reply,
                    'depth' => $depth + 1
                    ])
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>