@php
$assignment = $template?->assignments?->first();
$defaultQuestions = $template?->questions
    ? $template->questions
        ->sortBy('order_number')
        ->values()
        ->map(fn ($question) => [
            'label' => $question->label,
            'description' => $question->description,
            'type' => $question->type,
            'options' => $question->options ?? new stdClass(),
            'is_required' => (bool) $question->is_required,
        ])
        ->all()
    : [[
        'label' => '',
        'description' => '',
        'type' => 'textarea',
        'options' => ['placeholder' => 'Tuliskan refleksi siswa...', 'min_length' => '10', 'max_length' => '500'],
        'is_required' => true,
    ]];
$initialQuestions = collect(old('questions', $defaultQuestions))
    ->map(function ($question) {
        if (is_string($question['options'] ?? null)) {
            $question['options'] = json_decode($question['options'], true) ?: [];
        }

        return [
            'label' => $question['label'] ?? '',
            'description' => $question['description'] ?? '',
            'type' => $question['type'] ?? 'textarea',
            'options' => $question['options'] ?? [],
            'is_required' => !empty($question['is_required']),
        ];
    })
    ->values()
    ->all();
@endphp

@if(session('error'))
<div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4">
    <div class="flex items-center gap-2 text-sm font-semibold text-red-800">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>Periksa kembali builder template sebelum menyimpan.</span>
    </div>
    <ul class="mt-3 ml-5 list-disc space-y-1 text-sm text-red-700">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if($method !== 'POST')
    @method($method)
    @endif

    <input type="hidden" name="submit_mode" value="draft" data-submit-mode-input>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Reflection Template Builder</h2>
            <p class="mt-1 text-sm text-slate-500">
                Susun template seperti CMS: tambah komponen, atur properti visual, dan pantau preview siswa secara langsung.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.reflection-templates.index') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
            </a>
            <button type="button" data-preview-trigger
                class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <i class="fa-regular fa-eye mr-2"></i> Preview
            </button>
            <button type="submit" data-submit-mode="draft"
                class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">
                <i class="fa-regular fa-floppy-disk mr-2"></i> Simpan Draft
            </button>
            <button type="submit" data-submit-mode="publish"
                class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                <i class="fa-solid fa-bullhorn mr-2"></i> Publish
            </button>
        </div>
    </div>

    <div
        class="space-y-6"
        data-reflection-template-builder>
        <script type="application/json" data-builder-initial-questions>@json($initialQuestions)</script>
        <script type="application/json" data-builder-errors>@json($errors->toArray())</script>

        <div class="grid gap-6 2xl:grid-cols-[300px_minmax(0,1.3fr)_minmax(360px,0.9fr)]">
            <aside class="space-y-6 xl:sticky xl:top-6 self-start">
                <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">Component Library</h3>
                            <p class="mt-1 text-sm text-slate-500">Pilih komponen yang ingin ditambahkan ke template.</p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">No-code</span>
                    </div>
                    <div class="mt-5 space-y-3" data-builder-library></div>
                </section>

                <section class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 p-5 text-white shadow-sm">
                    <h3 class="text-base font-semibold">Tips Builder</h3>
                    <ul class="mt-4 space-y-3 text-sm text-slate-200">
                        <li class="flex gap-3">
                            <i class="fa-solid fa-grip-vertical mt-0.5 text-slate-400"></i>
                            <span>Geser card komponen untuk mengubah urutan pertanyaan.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fa-regular fa-copy mt-0.5 text-slate-400"></i>
                            <span>Gunakan duplicate untuk mempercepat pembuatan blok pertanyaan yang mirip.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fa-regular fa-eye mt-0.5 text-slate-400"></i>
                            <span>Preview akan selalu diperbarui berdasarkan state builder saat ini.</span>
                        </li>
                    </ul>
                </section>
            </aside>

            <div class="space-y-6">
                <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Template Meta</h3>
                            <p class="mt-1 text-sm text-slate-500">Informasi utama template dan assignment global.</p>
                        </div>
                        <span class="rounded-full {{ old('submit_mode') === 'publish' || $template?->is_active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }} px-3 py-1 text-xs font-semibold">
                            {{ $template?->is_active ? 'Sedang Aktif' : 'Mode Draft' }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <label class="block md:col-span-2">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Judul Template</span>
                            <input type="text" id="title" name="title" value="{{ old('title', $template?->title) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="Contoh: Reconnect with My Feelings" required>
                        </label>

                        <label class="block md:col-span-2">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</span>
                            <textarea id="description" name="description" rows="3"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="Jelaskan tujuan template ini...">{{ old('description', $template?->description) }}</textarea>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Periode Refleksi</span>
                            <select id="period_type" name="period_type"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                required>
                                @foreach(['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'custom' => 'Custom'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('period_type', $template?->period_type ?? 'daily') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="text-sm font-medium text-slate-700">Assignment Scope</div>
                            <div class="mt-2 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                Semua Siswa
                            </div>
                            <p class="mt-2 text-xs leading-5 text-slate-500">V1 builder admin mengelola satu assignment global untuk seluruh siswa.</p>
                        </div>

                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Tanggal Mulai Assignment</span>
                            <input type="date" id="assignment_start_date" name="assignment_start_date"
                                value="{{ old('assignment_start_date', $assignment?->start_date?->format('Y-m-d')) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Tanggal Selesai Assignment</span>
                            <input type="date" id="assignment_end_date" name="assignment_end_date"
                                value="{{ old('assignment_end_date', $assignment?->end_date?->format('Y-m-d')) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>
                    </div>

                    <div class="mt-5 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        Untuk periode <strong>custom</strong>, tanggal mulai assignment wajib diisi. Gunakan tombol <strong>Publish</strong> jika template siap dipakai siswa.
                    </div>
                </section>

                <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Canvas Builder</h3>
                            <p class="mt-1 text-sm text-slate-500">Susun komponen pertanyaan, ubah properti, dan reorder dengan drag-and-drop.</p>
                        </div>
                        <div class="flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            <i class="fa-solid fa-grip-vertical"></i>
                            Drag untuk ubah urutan
                        </div>
                    </div>

                    <div class="mt-5" data-builder-canvas></div>
                    <div data-builder-hidden-inputs hidden></div>
                </section>
            </div>

            <aside class="space-y-6">
                <div class="sticky top-6" data-builder-preview></div>
            </aside>
        </div>
    </div>
</form>
