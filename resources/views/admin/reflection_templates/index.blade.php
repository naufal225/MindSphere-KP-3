@extends('components.admin.layout.app')

@section('title', 'Template Refleksi')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Template Refleksi</h1>
            <p class="text-gray-600">Kelola template refleksi dinamis untuk seluruh siswa.</p>
        </div>
        <a href="{{ route('admin.reflection-templates.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-2xl hover:bg-blue-700 shadow-sm">
            <i class="mr-2 fa-solid fa-plus"></i> Buat Template
        </a>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 text-green-700 border border-green-200 rounded-lg bg-green-50">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="px-4 py-3 text-red-700 border border-red-200 rounded-lg bg-red-50">
        {{ session('error') }}
    </div>
    @endif

    <div class="p-6 bg-white border border-gray-100 rounded-lg shadow-sm">
        <form method="GET" action="{{ route('admin.reflection-templates.index') }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label for="search" class="block mb-2 text-sm font-medium text-gray-700">Cari Template</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Judul atau deskripsi..."
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="period_type" class="block mb-2 text-sm font-medium text-gray-700">Periode</label>
                    <select id="period_type" name="period_type"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Periode</option>
                        @foreach(['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'custom' => 'Custom'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('period_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="mr-2 fa-solid fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.reflection-templates.index') }}"
                        class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
            <p class="text-sm text-gray-500">Total Template</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $templates->total() }}</p>
        </div>
        <div class="p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
            <p class="text-sm text-gray-500">Template Aktif</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $templates->getCollection()->where('is_active', true)->count() }}</p>
        </div>
        <div class="p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
            <p class="text-sm text-gray-500">Submission Terkait</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $templates->getCollection()->sum('student_reflections_count') }}</p>
        </div>
    </div>

    <div class="overflow-hidden bg-white border border-gray-100 rounded-[28px] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Template</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Assignment</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Pertanyaan</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($templates as $template)
                    @php($assignment = $template->assignments->first())
                    <tr class="align-top transition-colors hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                    <i class="fa-solid fa-note-sticky"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900">{{ $template->title }}</div>
                                    <div class="mt-1 text-sm text-gray-500">{{ $template->description ?: 'Tanpa deskripsi' }}</div>
                                    <div class="mt-2 text-xs text-gray-400">Dibuat oleh {{ $template->createdBy?->name ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="inline-flex px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-medium">
                                {{ ucfirst($template->period_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div class="font-medium text-gray-900">Semua siswa</div>
                            <div class="text-xs text-gray-500">
                                {{ $assignment?->start_date?->format('d M Y') ?? 'Tanpa mulai' }}
                                -
                                {{ $assignment?->end_date?->format('d M Y') ?? 'Tanpa akhir' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div>{{ $template->questions_count }} pertanyaan</div>
                            <div class="text-xs text-gray-500">{{ $template->student_reflections_count }} submission</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($template->is_active)
                            <span class="inline-flex px-3 py-1 text-sm font-medium text-green-700 rounded-full bg-green-50">Aktif</span>
                            @else
                            <span class="inline-flex px-3 py-1 text-sm font-medium text-gray-700 rounded-full bg-gray-100">Tidak aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                <a href="{{ route('admin.reflection-templates.edit', $template->id) }}"
                                    class="inline-flex items-center px-3 py-2 text-sm text-yellow-700 rounded-xl bg-yellow-50 hover:bg-yellow-100">
                                    <i class="mr-2 fa-solid fa-pen-to-square"></i> Edit Builder
                                </a>
                                <form method="POST" action="{{ route('admin.reflection-templates.duplicate', $template->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-2 text-sm text-slate-700 rounded-xl bg-slate-100 hover:bg-slate-200">
                                        <i class="mr-2 fa-regular fa-copy"></i> Duplikat
                                    </button>
                                </form>
                                @if($template->is_active)
                                <form method="POST" action="{{ route('admin.reflection-templates.unpublish', $template->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-2 text-sm text-orange-700 rounded-xl bg-orange-50 hover:bg-orange-100">
                                        <i class="mr-2 fa-solid fa-toggle-off"></i> Nonaktifkan
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.reflection-templates.publish', $template->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-2 text-sm text-green-700 rounded-xl bg-green-50 hover:bg-green-100">
                                        <i class="mr-2 fa-solid fa-bullhorn"></i> Publish
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('admin.reflection-templates.destroy', $template->id) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-2 text-sm text-red-700 rounded-xl bg-red-50 hover:bg-red-100">
                                        <i class="mr-2 fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Belum ada template refleksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($templates->hasPages())
    <div>
        {{ $templates->links() }}
    </div>
    @endif
</div>
@endsection
