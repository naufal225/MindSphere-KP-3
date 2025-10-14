@php
$isReply = isset($parentId) && $parentId;
$fieldName = $isReply ? "content_{$parentId}" : 'content_main';
@endphp

<form action="{{ route('admin.forum.comments.store', $post->id) }}" method="POST"
    class="{{ $isReply ? 'bg-blue-50 p-4 rounded-lg border border-blue-200 mt-3' : 'bg-gray-50 p-4 rounded-lg border border-gray-200' }}">
    @csrf

    @if($isReply)
    <input type="hidden" name="parent_id" value="{{ $parentId }}">
    <div class="flex items-center gap-2 text-blue-700 text-sm font-medium mb-3">
        <i class="fas fa-reply"></i>
        <span>Membalas komentar</span>
    </div>
    @endif

    <div class="space-y-3">
        <div>
            <label for="{{ $fieldName }}" class="block text-sm font-medium text-neutral-700 mb-1">
                Komentar {{ $isReply ? 'Balasan' : '' }}
            </label>
            <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" rows="3"
                placeholder="{{ $isReply ? 'Tulis balasan Anda...' : 'Tulis komentar Anda...' }}"
                class="comment-textarea w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                required>{{ old($fieldName) }}</textarea>

            @error($fieldName)
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center">
            <div class="text-sm text-neutral-500">
                <i class="fas fa-info-circle mr-1"></i>
                Komentar Anda akan ditampilkan sebagai Administrator
            </div>
            <div class="flex gap-2">
                @if($isReply)
                <button type="button" onclick="toggleReplyForm({{ $parentId }})"
                    class="px-4 py-2 text-sm text-neutral-600 hover:text-neutral-800 transition-colors">
                    Batal
                </button>
                @endif
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    {{ $isReply ? 'Kirim Balasan' : 'Kirim Komentar' }}
                </button>
            </div>
        </div>
    </div>
</form>