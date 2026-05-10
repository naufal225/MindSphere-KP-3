import Sortable from 'sortablejs';

const QUESTION_LIBRARY = [
    { type: 'text', label: 'Text', icon: 'fa-i-cursor', description: 'Jawaban singkat satu baris.' },
    { type: 'textarea', label: 'Textarea', icon: 'fa-align-left', description: 'Jawaban panjang beberapa baris.' },
    { type: 'single_choice', label: 'Single Choice', icon: 'fa-circle-dot', description: 'Pilih satu jawaban.' },
    { type: 'multiple_choice', label: 'Multiple Choice', icon: 'fa-square-check', description: 'Pilih beberapa jawaban.' },
    { type: 'scale', label: 'Scale', icon: 'fa-sliders', description: 'Skala angka umum.' },
    { type: 'mood_scale', label: 'Mood Scale', icon: 'fa-face-smile', description: 'Skala suasana hati.' },
    { type: 'emotion_picker', label: 'Emotion Picker', icon: 'fa-icons', description: 'Pilih emosi dengan emoji.' },
    { type: 'emotion_table', label: 'Emotion Table', icon: 'fa-table-cells', description: 'Tabel refleksi emosi.' },
    { type: 'number', label: 'Number', icon: 'fa-hashtag', description: 'Input angka dengan batas.' },
    { type: 'date_range', label: 'Date Range', icon: 'fa-calendar-days', description: 'Rentang tanggal.' },
];

let builderSequence = 0;

function initReflectionTemplateBuilder() {
    const root = document.querySelector('[data-reflection-template-builder]');

    if (!root) {
        return;
    }

    const form = root.closest('form');
    const canvas = root.querySelector('[data-builder-canvas]');
    const hiddenInputs = root.querySelector('[data-builder-hidden-inputs]');
    const library = root.querySelector('[data-builder-library]');
    const preview = root.querySelector('[data-builder-preview]');
    const previewButton = form?.querySelector('[data-preview-trigger]');
    const questionsJson = root.querySelector('[data-builder-initial-questions]');
    const errorsJson = root.querySelector('[data-builder-errors]');

    const state = {
        questions: parseJsonScript(questionsJson, []).map((question) => normalizeQuestion(question)),
        serverErrors: parseJsonScript(errorsJson, {}),
        clientErrors: {},
        previewVisible: true,
    };

    let sortable = null;

    function render() {
        renderLibrary();
        renderCanvas();
        renderHiddenInputs();
        renderPreview();
    }

    function renderLibrary() {
        library.innerHTML = QUESTION_LIBRARY.map((item) => `
            <button type="button"
                class="group flex w-full items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md"
                data-action="add-question"
                data-type="${escapeHtml(item.type)}">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <i class="fa-solid ${escapeHtml(item.icon)}"></i>
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-semibold text-slate-900">${escapeHtml(item.label)}</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">${escapeHtml(item.description)}</span>
                </span>
                <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-500 group-hover:bg-blue-100 group-hover:text-blue-700">
                    Add
                </span>
            </button>
        `).join('');
    }

    function renderCanvas() {
        const generalErrors = state.clientErrors.questions || [];

        canvas.innerHTML = `
            ${generalErrors.length ? `
                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    ${generalErrors.map((message) => `<div>${escapeHtml(message)}</div>`).join('')}
                </div>
            ` : ''}
            ${state.questions.length ? state.questions.map((question, index) => renderQuestionCard(question, index)).join('') : `
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
                        <i class="fa-solid fa-layer-group text-2xl"></i>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-slate-800">Canvas masih kosong</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Tambahkan komponen dari panel kiri untuk mulai menyusun template refleksi seperti sebuah CMS.
                    </p>
                </div>
            `}
        `;

        if (!sortable) {
            sortable = Sortable.create(canvas, {
                animation: 180,
                handle: '[data-drag-handle]',
                ghostClass: 'reflection-builder-ghost',
                chosenClass: 'reflection-builder-chosen',
                dragClass: 'reflection-builder-drag',
                onEnd: () => {
                    const orderedIds = [...canvas.querySelectorAll('[data-question-card]')].map((element) => element.dataset.builderId);
                    state.questions.sort((a, b) => orderedIds.indexOf(a.builderId) - orderedIds.indexOf(b.builderId));
                    syncServerErrorsByOrder();
                    render();
                },
            });
        }
    }

    function renderQuestionCard(question, index) {
        const cardErrors = getQuestionErrors(index, question.builderId);
        const typeMeta = QUESTION_LIBRARY.find((item) => item.type === question.type);

        return `
            <section class="mb-4 rounded-3xl border border-slate-200 bg-white shadow-sm" data-question-card data-builder-id="${escapeHtml(question.builderId)}">
                <header class="flex flex-wrap items-start gap-3 border-b border-slate-100 px-5 py-4">
                    <button type="button" class="mt-1 cursor-grab rounded-xl border border-slate-200 bg-slate-50 p-2 text-slate-500 hover:bg-slate-100" data-drag-handle title="Geser urutan">
                        <i class="fa-solid fa-grip-vertical"></i>
                    </button>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <i class="fa-solid ${escapeHtml(typeMeta?.icon || 'fa-circle-question')}"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-base font-semibold text-slate-900">
                                <span data-question-heading>${escapeHtml(question.label || `Komponen ${index + 1}`)}</span>
                            </h3>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-slate-600">
                                ${escapeHtml(question.type)}
                            </span>
                            ${question.is_required ? '<span class="rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-600">Required</span>' : ''}
                            ${cardErrors.length ? `<span class="rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-semibold text-red-600">${cardErrors.length} error</span>` : ''}
                        </div>
                        <p class="mt-1 text-sm text-slate-500">
                            <span data-question-summary>${escapeHtml(question.description || typeMeta?.description || 'Atur properti komponen ini melalui panel editor di bawah.')}</span>
                        </p>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50" data-action="duplicate-question" data-builder-id="${escapeHtml(question.builderId)}">
                            <i class="fa-regular fa-copy mr-1"></i> Duplicate
                        </button>
                        <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50" data-action="toggle-collapse" data-builder-id="${escapeHtml(question.builderId)}">
                            <i class="fa-solid ${question.collapsed ? 'fa-chevron-down' : 'fa-chevron-up'} mr-1"></i>
                            ${question.collapsed ? 'Expand' : 'Collapse'}
                        </button>
                        <button type="button" class="rounded-xl border border-red-200 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50" data-action="remove-question" data-builder-id="${escapeHtml(question.builderId)}">
                            <i class="fa-regular fa-trash-can mr-1"></i> Delete
                        </button>
                    </div>
                </header>
                ${question.collapsed ? '' : `
                    <div class="space-y-5 px-5 py-5">
                        ${cardErrors.length ? `
                            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                ${cardErrors.map((message) => `<div>${escapeHtml(message)}</div>`).join('')}
                            </div>
                        ` : ''}
                        ${renderBaseFields(question)}
                        ${renderTypeSpecificFields(question)}
                    </div>
                `}
            </section>
        `;
    }

    function renderBaseFields(question) {
        return `
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block md:col-span-2">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Label Pertanyaan</span>
                    <input type="text"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(question.label)}"
                        data-field="label"
                        data-builder-id="${escapeHtml(question.builderId)}"
                        placeholder="Contoh: Apa yang paling kamu rasakan hari ini?">
                </label>
                <label class="block md:col-span-2">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Deskripsi / Helper Text</span>
                    <textarea rows="3"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        data-field="description"
                        data-builder-id="${escapeHtml(question.builderId)}"
                        placeholder="Petunjuk tambahan untuk siswa...">${escapeHtml(question.description)}</textarea>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Tipe Komponen</span>
                    <select
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        data-field="type"
                        data-builder-id="${escapeHtml(question.builderId)}">
                        ${QUESTION_LIBRARY.map((item) => `
                            <option value="${escapeHtml(item.type)}" ${item.type === question.type ? 'selected' : ''}>
                                ${escapeHtml(item.label)}
                            </option>
                        `).join('')}
                    </select>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Status Input</span>
                    <div class="flex h-[52px] items-center rounded-2xl border border-slate-200 bg-white px-4 shadow-sm">
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                            <input type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                data-field="is_required"
                                data-builder-id="${escapeHtml(question.builderId)}"
                                ${question.is_required ? 'checked' : ''}>
                            Wajib diisi siswa
                        </label>
                    </div>
                </label>
            </div>
        `;
    }

    function renderTypeSpecificFields(question) {
        switch (question.type) {
            case 'text':
            case 'textarea':
                return `
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <h4 class="text-sm font-semibold text-slate-900">Pengaturan Teks</h4>
                        <div class="mt-4 grid gap-4 md:grid-cols-3">
                            ${renderSimpleOptionInput(question, 'placeholder', 'Placeholder', 'Contoh: Tulis jawabanmu di sini...')}
                            ${renderSimpleOptionInput(question, 'min_length', 'Min Length', '0', 'number')}
                            ${renderSimpleOptionInput(question, 'max_length', 'Max Length', '500', 'number')}
                        </div>
                    </div>
                `;
            case 'number':
                return `
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <h4 class="text-sm font-semibold text-slate-900">Pengaturan Angka</h4>
                        <div class="mt-4 grid gap-4 md:grid-cols-4">
                            ${renderSimpleOptionInput(question, 'placeholder', 'Placeholder', 'Contoh: 1')}
                            ${renderSimpleOptionInput(question, 'min', 'Min', '0', 'number')}
                            ${renderSimpleOptionInput(question, 'max', 'Max', '10', 'number')}
                            ${renderSimpleOptionInput(question, 'step', 'Step', '1', 'number')}
                        </div>
                    </div>
                `;
            case 'scale':
            case 'mood_scale':
                return `
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <h4 class="text-sm font-semibold text-slate-900">Pengaturan Scale</h4>
                        <div class="mt-4 grid gap-4 md:grid-cols-4">
                            ${renderSimpleOptionInput(question, 'min', 'Nilai Minimum', '1', 'number')}
                            ${renderSimpleOptionInput(question, 'max', 'Nilai Maksimum', '5', 'number')}
                            ${renderSimpleOptionInput(question, 'min_label', 'Label Minimum', 'Rendah')}
                            ${renderSimpleOptionInput(question, 'max_label', 'Label Maksimum', 'Tinggi')}
                        </div>
                    </div>
                `;
            case 'single_choice':
            case 'multiple_choice':
                return `
                    <div class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-slate-900">Daftar Pilihan</h4>
                            <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100" data-action="add-choice" data-builder-id="${escapeHtml(question.builderId)}">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah Opsi
                            </button>
                        </div>
                        <div class="space-y-3">
                            ${question.options.choices.map((choice, choiceIndex) => renderChoiceRow(question, choice, choiceIndex)).join('')}
                        </div>
                        ${question.type === 'multiple_choice' ? `
                            <div class="grid gap-4 md:grid-cols-2">
                                ${renderSimpleOptionInput(question, 'min_select', 'Minimum Pilihan', '1', 'number')}
                                ${renderSimpleOptionInput(question, 'max_select', 'Maksimum Pilihan', '3', 'number')}
                            </div>
                        ` : ''}
                    </div>
                `;
            case 'emotion_picker':
                return `
                    <div class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-slate-900">Daftar Emosi</h4>
                            <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100" data-action="add-emotion-choice" data-builder-id="${escapeHtml(question.builderId)}">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah Emosi
                            </button>
                        </div>
                        <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">
                            <input type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                data-option-key="allow_multiple"
                                data-builder-id="${escapeHtml(question.builderId)}"
                                ${question.options.allow_multiple ? 'checked' : ''}>
                            Boleh pilih lebih dari satu emosi
                        </label>
                        <div class="space-y-3">
                            ${question.options.choices.map((choice, choiceIndex) => renderEmotionChoiceRow(question, choice, choiceIndex)).join('')}
                        </div>
                    </div>
                `;
            case 'emotion_table':
                return `
                    <div class="grid gap-4 xl:grid-cols-2">
                        <div class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-slate-900">Baris Emosi</h4>
                                <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100" data-action="add-table-emotion" data-builder-id="${escapeHtml(question.builderId)}">
                                    <i class="fa-solid fa-plus mr-1"></i> Tambah Emosi
                                </button>
                            </div>
                            <div class="space-y-3">
                                ${question.options.emotions.map((emotion, emotionIndex) => renderTableEmotionRow(question, emotion, emotionIndex)).join('')}
                            </div>
                        </div>
                        <div class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-slate-900">Kolom Tabel</h4>
                                <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100" data-action="add-row-field" data-builder-id="${escapeHtml(question.builderId)}">
                                    <i class="fa-solid fa-plus mr-1"></i> Tambah Kolom
                                </button>
                            </div>
                            <div class="space-y-3">
                                ${question.options.row_fields.map((field, fieldIndex) => renderRowFieldRow(question, field, fieldIndex)).join('')}
                            </div>
                        </div>
                    </div>
                `;
            case 'date_range':
                return `
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <h4 class="text-sm font-semibold text-slate-900">Label Tanggal</h4>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            ${renderSimpleOptionInput(question, 'start_label', 'Label Tanggal Mulai', 'Tanggal mulai')}
                            ${renderSimpleOptionInput(question, 'end_label', 'Label Tanggal Selesai', 'Tanggal selesai')}
                        </div>
                    </div>
                `;
            default:
                return '';
        }
    }

    function renderSimpleOptionInput(question, optionKey, label, placeholder, type = 'text') {
        const value = question.options[optionKey] ?? '';

        return `
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-slate-700">${escapeHtml(label)}</span>
                <input type="${escapeHtml(type)}"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    value="${escapeHtml(String(value))}"
                    placeholder="${escapeHtml(placeholder)}"
                    data-option-key="${escapeHtml(optionKey)}"
                    data-builder-id="${escapeHtml(question.builderId)}">
            </label>
        `;
    }

    function renderChoiceRow(question, choice, choiceIndex) {
        return `
            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Value</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(choice.value || '')}"
                        data-array-name="choices"
                        data-array-index="${choiceIndex}"
                        data-array-prop="value"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Label</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(choice.label || '')}"
                        data-array-name="choices"
                        data-array-index="${choiceIndex}"
                        data-array-prop="label"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <div class="flex items-end">
                    <button type="button" class="rounded-xl border border-red-200 px-3 py-2 text-sm text-red-600 hover:bg-red-50" data-action="remove-choice" data-builder-id="${escapeHtml(question.builderId)}" data-index="${choiceIndex}">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function renderEmotionChoiceRow(question, choice, choiceIndex) {
        return `
            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-[80px_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto]">
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Emoji</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-center text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(choice.emoji || '')}"
                        data-array-name="choices"
                        data-array-index="${choiceIndex}"
                        data-array-prop="emoji"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Value</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(choice.value || '')}"
                        data-array-name="choices"
                        data-array-index="${choiceIndex}"
                        data-array-prop="value"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Label</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(choice.label || '')}"
                        data-array-name="choices"
                        data-array-index="${choiceIndex}"
                        data-array-prop="label"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Color</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(choice.color || '')}"
                        placeholder="#F59E0B"
                        data-array-name="choices"
                        data-array-index="${choiceIndex}"
                        data-array-prop="color"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <div class="flex items-end">
                    <button type="button" class="rounded-xl border border-red-200 px-3 py-2 text-sm text-red-600 hover:bg-red-50" data-action="remove-emotion-choice" data-builder-id="${escapeHtml(question.builderId)}" data-index="${choiceIndex}">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function renderTableEmotionRow(question, emotion, emotionIndex) {
        return `
            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-[80px_minmax(0,1fr)_minmax(0,1fr)_auto]">
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Emoji</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-center text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(emotion.emoji || '')}"
                        data-array-name="emotions"
                        data-array-index="${emotionIndex}"
                        data-array-prop="emoji"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Value</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(emotion.value || '')}"
                        data-array-name="emotions"
                        data-array-index="${emotionIndex}"
                        data-array-prop="value"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Label</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(emotion.label || '')}"
                        data-array-name="emotions"
                        data-array-index="${emotionIndex}"
                        data-array-prop="label"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <div class="flex items-end">
                    <button type="button" class="rounded-xl border border-red-200 px-3 py-2 text-sm text-red-600 hover:bg-red-50" data-action="remove-table-emotion" data-builder-id="${escapeHtml(question.builderId)}" data-index="${emotionIndex}">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function renderRowFieldRow(question, field, fieldIndex) {
        return `
            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_160px_auto]">
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Key</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(field.key || '')}"
                        data-array-name="row_fields"
                        data-array-index="${fieldIndex}"
                        data-array-prop="key"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Label</span>
                    <input type="text"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        value="${escapeHtml(field.label || '')}"
                        data-array-name="row_fields"
                        data-array-index="${fieldIndex}"
                        data-array-prop="label"
                        data-builder-id="${escapeHtml(question.builderId)}">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tipe</span>
                    <select
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        data-array-name="row_fields"
                        data-array-index="${fieldIndex}"
                        data-array-prop="type"
                        data-builder-id="${escapeHtml(question.builderId)}">
                        ${['text', 'textarea', 'number'].map((type) => `
                            <option value="${type}" ${field.type === type ? 'selected' : ''}>${type}</option>
                        `).join('')}
                    </select>
                </label>
                <div class="flex items-end">
                    <button type="button" class="rounded-xl border border-red-200 px-3 py-2 text-sm text-red-600 hover:bg-red-50" data-action="remove-row-field" data-builder-id="${escapeHtml(question.builderId)}" data-index="${fieldIndex}">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function renderHiddenInputs() {
        hiddenInputs.innerHTML = state.questions.map((question, index) => {
            return [
                hiddenInput(`questions[${index}][label]`, question.label),
                hiddenInput(`questions[${index}][description]`, question.description),
                hiddenInput(`questions[${index}][type]`, question.type),
                hiddenInput(`questions[${index}][is_required]`, question.is_required ? '1' : '0'),
                hiddenInput(`questions[${index}][options]`, JSON.stringify(cleanQuestionOptions(question.type, question.options))),
            ].join('');
        }).join('');
    }

    function renderPreview() {
        preview.innerHTML = state.questions.length ? `
            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Live Preview</h3>
                        <p class="mt-1 text-sm text-slate-500">Simulasi tampilan form siswa berdasarkan builder saat ini.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">${state.questions.length} komponen</span>
                </div>
                <div class="mt-5 space-y-4">
                    ${state.questions.map((question, index) => renderPreviewQuestion(question, index)).join('')}
                </div>
            </div>
        ` : `
            <div class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-400">
                    <i class="fa-regular fa-eye text-2xl"></i>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-slate-900">Preview akan muncul di sini</h3>
                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                    Tambahkan komponen dari library lalu atur propertinya untuk melihat simulasi form siswa secara langsung.
                </p>
            </div>
        `;
    }

    function renderPreviewQuestion(question, index) {
        return `
            <section class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">${index + 1}. ${escapeHtml(question.label || 'Pertanyaan tanpa judul')}</div>
                        ${question.description ? `<p class="mt-1 text-xs leading-5 text-slate-500">${escapeHtml(question.description)}</p>` : ''}
                    </div>
                    ${question.is_required ? '<span class="rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-600">Required</span>' : ''}
                </div>
                <div class="mt-4">
                    ${renderPreviewField(question)}
                </div>
            </section>
        `;
    }

    function renderPreviewField(question) {
        const options = cleanQuestionOptions(question.type, question.options);

        switch (question.type) {
            case 'text':
            case 'number':
                return `<input disabled type="${question.type === 'number' ? 'number' : 'text'}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500" placeholder="${escapeHtml(options.placeholder || '')}">`;
            case 'textarea':
                return `<textarea disabled rows="4" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500" placeholder="${escapeHtml(options.placeholder || '')}"></textarea>`;
            case 'single_choice':
                return `<div class="space-y-2">${(options.choices || []).map((choice) => `
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                        <input type="radio" disabled>
                        <span>${escapeHtml(choice.label || choice.value || '')}</span>
                    </label>`).join('')}</div>`;
            case 'multiple_choice':
                return `<div class="space-y-2">${(options.choices || []).map((choice) => `
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                        <input type="checkbox" disabled>
                        <span>${escapeHtml(choice.label || choice.value || '')}</span>
                    </label>`).join('')}</div>`;
            case 'scale':
            case 'mood_scale':
                return `
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                        <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                            <span>${escapeHtml(String(options.min_label || options.min || '1'))}</span>
                            <span>${escapeHtml(String(options.max_label || options.max || '5'))}</span>
                        </div>
                        <input type="range" disabled min="${escapeHtml(String(options.min || 1))}" max="${escapeHtml(String(options.max || 5))}" value="${escapeHtml(String(options.min || 1))}" class="mt-3 w-full">
                    </div>
                `;
            case 'emotion_picker':
                return `<div class="flex flex-wrap gap-2">${(options.choices || []).map((choice) => `
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                        ${escapeHtml(choice.emoji || '🙂')} ${escapeHtml(choice.label || choice.value || '')}
                    </span>`).join('')}</div>`;
            case 'emotion_table':
                return `
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Emosi</th>
                                    ${(options.row_fields || []).map((field) => `<th class="px-4 py-3 text-left font-semibold text-slate-600">${escapeHtml(field.label || field.key || '')}</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                ${(options.emotions || []).map((emotion) => `
                                    <tr>
                                        <td class="px-4 py-3 text-slate-700">${escapeHtml(emotion.emoji || '🙂')} ${escapeHtml(emotion.label || emotion.value || '')}</td>
                                        ${(options.row_fields || []).map(() => `<td class="px-4 py-3 text-slate-400">...</td>`).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            case 'date_range':
                return `
                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">${escapeHtml(options.start_label || 'Tanggal mulai')}</div>
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">${escapeHtml(options.end_label || 'Tanggal selesai')}</div>
                    </div>
                `;
            default:
                return '<div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">Preview tidak tersedia.</div>';
        }
    }

    function updateQuestion(questionId, updater) {
        state.questions = state.questions.map((question) => {
            if (question.builderId !== questionId) {
                return question;
            }

            const updated = updater(structuredCloneQuestion(question));
            return normalizeQuestion(updated, question.builderId);
        });

        clearErrorsForQuestion(questionId);
        render();
    }

    function patchQuestion(questionId, updater) {
        const question = state.questions.find((item) => item.builderId === questionId);

        if (!question) {
            return null;
        }

        updater(question);
        const normalized = normalizeQuestion(question, question.builderId);
        Object.assign(question, normalized);
        clearErrorsForQuestion(questionId);

        return question;
    }

    function clearErrorsForQuestion(questionId) {
        Object.keys(state.clientErrors).forEach((key) => {
            if (key.startsWith(`questions.`)) {
                const parts = key.split('.');
                const index = Number(parts[1]);
                const question = state.questions[index];

                if (question?.builderId === questionId) {
                    delete state.clientErrors[key];
                }
            }
        });

        Object.keys(state.serverErrors).forEach((key) => {
            if (key.startsWith(`questions.`)) {
                const parts = key.split('.');
                const index = Number(parts[1]);
                const question = state.questions[index];

                if (question?.builderId === questionId) {
                    delete state.serverErrors[key];
                }
            }
        });
    }

    function syncServerErrorsByOrder() {
        // Keep server errors from old request only on same visual order.
        // Once user reorders components, clear stale server-side question errors to avoid wrong mapping.
        state.serverErrors = Object.fromEntries(
            Object.entries(state.serverErrors).filter(([key]) => !key.startsWith('questions.'))
        );
    }

    function getQuestionErrors(index, questionId) {
        void questionId;
        const errors = [];
        const serverErrors = Object.entries(state.serverErrors)
            .filter(([key]) => key.startsWith(`questions.${index}.`))
            .flatMap(([, messages]) => messages);
        const clientErrors = Object.entries(state.clientErrors)
            .filter(([key]) => key.startsWith(`questions.${index}.`))
            .flatMap(([, messages]) => messages);

        errors.push(...serverErrors, ...clientErrors);

        return [...new Set(errors)];
    }

    function validateQuestions() {
        const errors = {};

        if (!state.questions.length) {
            errors.questions = ['Minimal harus ada satu komponen pertanyaan.'];
        }

        state.questions.forEach((question, index) => {
            const key = (suffix) => `questions.${index}.${suffix}`;

            if (!question.label.trim()) {
                errors[key('label')] = ['Label pertanyaan wajib diisi.'];
            }

            const options = cleanQuestionOptions(question.type, question.options);

            switch (question.type) {
                case 'single_choice':
                case 'multiple_choice':
                    if (!options.choices.length || options.choices.some((choice) => !choice.label?.trim() || !choice.value?.trim())) {
                        errors[key('options')] = ['Semua pilihan harus memiliki label dan value.'];
                    }
                    break;
                case 'emotion_picker':
                    if (!options.choices.length || options.choices.some((choice) => !choice.label?.trim() || !choice.value?.trim())) {
                        errors[key('options')] = ['Daftar emosi harus memiliki label dan value yang lengkap.'];
                    }
                    break;
                case 'emotion_table':
                    if (!options.emotions.length) {
                        errors[key('options')] = ['Emotion table wajib memiliki minimal satu baris emosi.'];
                    } else if (options.emotions.some((emotion) => !emotion.label?.trim() || !emotion.value?.trim())) {
                        errors[key('options')] = ['Semua emosi pada tabel harus memiliki label dan value.'];
                    }

                    if (!options.row_fields.length) {
                        errors[key('options')] = ['Emotion table wajib memiliki minimal satu kolom.'];
                    } else if (options.row_fields.some((field) => !field.key?.trim() || !field.label?.trim())) {
                        errors[key('options')] = ['Semua kolom pada emotion table harus memiliki key dan label.'];
                    }
                    break;
                case 'scale':
                case 'mood_scale': {
                    const min = Number(options.min);
                    const max = Number(options.max);
                    if (Number.isNaN(min) || Number.isNaN(max)) {
                        errors[key('options')] = ['Scale harus memiliki nilai minimum dan maksimum yang valid.'];
                    } else if (min >= max) {
                        errors[key('options')] = ['Nilai minimum scale harus lebih kecil dari maksimum.'];
                    }
                    break;
                }
                case 'date_range':
                    if (!String(options.start_label || '').trim() || !String(options.end_label || '').trim()) {
                        errors[key('options')] = ['Date range wajib memiliki label tanggal mulai dan selesai.'];
                    }
                    break;
                default:
                    break;
            }
        });

        state.clientErrors = errors;
        return !Object.keys(errors).length;
    }

    function submitBuilder(mode) {
        state.serverErrors = {};
        if (!validateQuestions()) {
            render();
            const firstError = canvas.querySelector('[data-question-card], .border-red-200');
            firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        const submitModeInput = form.querySelector('[data-submit-mode-input]');
        if (submitModeInput) {
            submitModeInput.value = mode;
        }

        renderHiddenInputs();
        return true;
    }

    function syncQuestionCardSummary(questionId) {
        const question = state.questions.find((item) => item.builderId === questionId);
        const card = canvas.querySelector(`[data-question-card][data-builder-id="${questionId}"]`);

        if (!question || !card) {
            return;
        }

        const heading = card.querySelector('[data-question-heading]');
        const summary = card.querySelector('[data-question-summary]');

        if (heading) {
            heading.textContent = question.label || `Komponen ${state.questions.findIndex((item) => item.builderId === questionId) + 1}`;
        }

        if (summary) {
            const typeMeta = QUESTION_LIBRARY.find((item) => item.type === question.type);
            summary.textContent = question.description || typeMeta?.description || 'Atur properti komponen ini melalui panel editor di bawah.';
        }
    }

    function syncPartialUi(questionId) {
        syncQuestionCardSummary(questionId);
        renderHiddenInputs();
        renderPreview();
    }

    library.addEventListener('click', (event) => {
        const button = event.target.closest('[data-action="add-question"]');
        if (!button) {
            return;
        }

        state.questions.push(createQuestion(button.dataset.type || 'textarea'));
        render();
    });

    canvas.addEventListener('click', (event) => {
        const actionElement = event.target.closest('[data-action]');

        if (!actionElement) {
            return;
        }

        const { action, builderId, index } = actionElement.dataset;
        const question = state.questions.find((item) => item.builderId === builderId);

        if (!question) {
            return;
        }

        switch (action) {
            case 'toggle-collapse':
                updateQuestion(builderId, (item) => {
                    item.collapsed = !item.collapsed;
                    return item;
                });
                break;
            case 'duplicate-question':
                {
                    const duplicated = normalizeQuestion(structuredCloneQuestion(question));
                    duplicated.label = duplicated.label ? `${duplicated.label} (Copy)` : 'Komponen Copy';
                    const currentIndex = state.questions.findIndex((item) => item.builderId === builderId);
                    state.questions.splice(currentIndex + 1, 0, duplicated);
                    render();
                }
                break;
            case 'remove-question':
                {
                    const hasContent = question.label.trim()
                        || question.description.trim()
                        || JSON.stringify(cleanQuestionOptions(question.type, question.options)) !== JSON.stringify(getDefaultQuestion(question.type).options);

                    if (hasContent && !window.confirm('Komponen ini sudah memiliki isi. Hapus komponen ini?')) {
                        return;
                    }

                    state.questions = state.questions.filter((item) => item.builderId !== builderId);
                    render();
                }
                break;
            case 'add-choice':
                updateQuestion(builderId, (item) => {
                    item.options.choices.push(defaultChoice(item.options.choices.length + 1));
                    return item;
                });
                break;
            case 'remove-choice':
                updateQuestion(builderId, (item) => {
                    item.options.choices.splice(Number(index), 1);
                    if (!item.options.choices.length) {
                        item.options.choices.push(defaultChoice(1));
                    }
                    return item;
                });
                break;
            case 'add-emotion-choice':
                updateQuestion(builderId, (item) => {
                    item.options.choices.push(defaultEmotionChoice(item.options.choices.length + 1));
                    return item;
                });
                break;
            case 'remove-emotion-choice':
                updateQuestion(builderId, (item) => {
                    item.options.choices.splice(Number(index), 1);
                    if (!item.options.choices.length) {
                        item.options.choices.push(defaultEmotionChoice(1));
                    }
                    return item;
                });
                break;
            case 'add-table-emotion':
                updateQuestion(builderId, (item) => {
                    item.options.emotions.push(defaultTableEmotion(item.options.emotions.length + 1));
                    return item;
                });
                break;
            case 'remove-table-emotion':
                updateQuestion(builderId, (item) => {
                    item.options.emotions.splice(Number(index), 1);
                    if (!item.options.emotions.length) {
                        item.options.emotions.push(defaultTableEmotion(1));
                    }
                    return item;
                });
                break;
            case 'add-row-field':
                updateQuestion(builderId, (item) => {
                    item.options.row_fields.push(defaultRowField(item.options.row_fields.length + 1));
                    return item;
                });
                break;
            case 'remove-row-field':
                updateQuestion(builderId, (item) => {
                    item.options.row_fields.splice(Number(index), 1);
                    if (!item.options.row_fields.length) {
                        item.options.row_fields.push(defaultRowField(1));
                    }
                    return item;
                });
                break;
            default:
                break;
        }
    });

    canvas.addEventListener('input', (event) => {
        const field = event.target.dataset.field;
        const optionKey = event.target.dataset.optionKey;
        const arrayName = event.target.dataset.arrayName;
        const builderId = event.target.dataset.builderId;

        if (!builderId) {
            return;
        }

        if (field) {
            const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
            const requiresFullRender = field === 'type' || event.target.type === 'checkbox' || event.target.tagName === 'SELECT';

            if (requiresFullRender) {
                updateQuestion(builderId, (item) => {
                    if (field === 'type') {
                        const nextType = String(value);
                        const defaults = getDefaultQuestion(nextType);
                        item.type = nextType;
                        item.options = defaults.options;
                    } else {
                        item[field] = value;
                    }
                    return item;
                });
            } else {
                patchQuestion(builderId, (item) => {
                    item[field] = value;
                });
                syncPartialUi(builderId);
            }
            return;
        }

        if (optionKey) {
            const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
            if (event.target.type === 'checkbox' || event.target.tagName === 'SELECT') {
                updateQuestion(builderId, (item) => {
                    item.options[optionKey] = value;
                    return item;
                });
            } else {
                patchQuestion(builderId, (item) => {
                    item.options[optionKey] = value;
                });
                syncPartialUi(builderId);
            }
            return;
        }

        if (arrayName) {
            const arrayIndex = Number(event.target.dataset.arrayIndex);
            const arrayProp = event.target.dataset.arrayProp;
            const value = event.target.value;

            patchQuestion(builderId, (item) => {
                item.options[arrayName][arrayIndex][arrayProp] = value;
            });
            syncPartialUi(builderId);
        }
    });

    previewButton?.addEventListener('click', () => {
        preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    form.addEventListener('submit', (event) => {
        const submitter = event.submitter;
        const mode = submitter?.dataset.submitMode || 'draft';

        if (!submitBuilder(mode)) {
            event.preventDefault();
        }
    });

    render();
}

function parseJsonScript(element, fallback) {
    if (!element) {
        return fallback;
    }

    try {
        return JSON.parse(element.textContent || 'null') ?? fallback;
    } catch (error) {
        return fallback;
    }
}

function createQuestion(type) {
    return normalizeQuestion(getDefaultQuestion(type));
}

function normalizeQuestion(question, builderId = null) {
    const type = question.type || 'textarea';
    const defaults = getDefaultQuestion(type);

    return {
        builderId: builderId || question.builderId || `builder_${Date.now()}_${builderSequence++}`,
        label: String(question.label || defaults.label || ''),
        description: String(question.description || ''),
        type,
        is_required: normalizeBoolean(question.is_required),
        options: mergeOptionsForType(type, question.options || defaults.options),
        collapsed: normalizeBoolean(question.collapsed),
    };
}

function getDefaultQuestion(type) {
    switch (type) {
        case 'text':
            return {
                label: '',
                description: '',
                type: 'text',
                is_required: false,
                options: { placeholder: 'Tulis jawaban singkat...', min_length: '', max_length: '' },
            };
        case 'textarea':
            return {
                label: '',
                description: '',
                type: 'textarea',
                is_required: true,
                options: { placeholder: 'Tuliskan refleksi siswa...', min_length: '10', max_length: '500' },
            };
        case 'number':
            return {
                label: '',
                description: '',
                type: 'number',
                is_required: false,
                options: { placeholder: '0', min: '', max: '', step: '1' },
            };
        case 'scale':
            return {
                label: '',
                description: '',
                type: 'scale',
                is_required: false,
                options: { min: '1', max: '5', min_label: 'Rendah', max_label: 'Tinggi' },
            };
        case 'mood_scale':
            return {
                label: '',
                description: '',
                type: 'mood_scale',
                is_required: false,
                options: { min: '1', max: '8', min_label: 'Ringan', max_label: 'Kuat' },
            };
        case 'single_choice':
            return {
                label: '',
                description: '',
                type: 'single_choice',
                is_required: true,
                options: { choices: [defaultChoice(1), defaultChoice(2)] },
            };
        case 'multiple_choice':
            return {
                label: '',
                description: '',
                type: 'multiple_choice',
                is_required: false,
                options: { choices: [defaultChoice(1), defaultChoice(2), defaultChoice(3)], min_select: '', max_select: '' },
            };
        case 'emotion_picker':
            return {
                label: '',
                description: '',
                type: 'emotion_picker',
                is_required: true,
                options: {
                    allow_multiple: false,
                    choices: [
                        { value: 'bahagia', label: 'Bahagia', emoji: '😊', color: '#F59E0B' },
                        { value: 'sedih', label: 'Sedih', emoji: '😔', color: '#3B82F6' },
                        { value: 'marah', label: 'Marah', emoji: '😡', color: '#EF4444' },
                    ],
                },
            };
        case 'emotion_table':
            return {
                label: '',
                description: '',
                type: 'emotion_table',
                is_required: true,
                options: {
                    emotions: [
                        { value: 'bahagia', label: 'Bahagia', emoji: '😊' },
                        { value: 'percaya', label: 'Percaya', emoji: '😉' },
                        { value: 'takut', label: 'Takut', emoji: '😨' },
                    ],
                    row_fields: [
                        { key: 'apa_terjadi', label: 'Apa yang terjadi', type: 'textarea' },
                        { key: 'dampak', label: 'Sensasi / Dampaknya', type: 'textarea' },
                        { key: 'solusi', label: 'Solusinya apa', type: 'textarea' },
                    ],
                },
            };
        case 'date_range':
            return {
                label: '',
                description: '',
                type: 'date_range',
                is_required: false,
                options: { start_label: 'Tanggal mulai', end_label: 'Tanggal selesai' },
            };
        default:
            return {
                label: '',
                description: '',
                type: 'textarea',
                is_required: false,
                options: { placeholder: 'Tuliskan refleksi siswa...', min_length: '10', max_length: '500' },
            };
    }
}

function defaultChoice(index) {
    return {
        value: `opsi_${index}`,
        label: `Opsi ${index}`,
    };
}

function defaultEmotionChoice(index) {
    const presets = [
        { value: 'bahagia', label: 'Bahagia', emoji: '😊', color: '#F59E0B' },
        { value: 'sedih', label: 'Sedih', emoji: '😔', color: '#3B82F6' },
        { value: 'marah', label: 'Marah', emoji: '😡', color: '#EF4444' },
        { value: 'tenang', label: 'Tenang', emoji: '😌', color: '#10B981' },
    ];

    return structuredCloneQuestion(presets[index - 1] || {
        value: `emosi_${index}`,
        label: `Emosi ${index}`,
        emoji: '🙂',
        color: '#94A3B8',
    });
}

function defaultTableEmotion(index) {
    const presets = [
        { value: 'bahagia', label: 'Bahagia', emoji: '😊' },
        { value: 'percaya', label: 'Percaya', emoji: '😉' },
        { value: 'takut', label: 'Takut', emoji: '😨' },
        { value: 'terkejut', label: 'Terkejut', emoji: '😮' },
    ];

    return structuredCloneQuestion(presets[index - 1] || {
        value: `emosi_${index}`,
        label: `Emosi ${index}`,
        emoji: '🙂',
    });
}

function defaultRowField(index) {
    const presets = [
        { key: 'apa_terjadi', label: 'Apa yang terjadi', type: 'textarea' },
        { key: 'dampak', label: 'Sensasi / Dampaknya', type: 'textarea' },
        { key: 'solusi', label: 'Solusinya apa', type: 'textarea' },
    ];

    return structuredCloneQuestion(presets[index - 1] || {
        key: `field_${index}`,
        label: `Kolom ${index}`,
        type: 'text',
    });
}

function mergeOptionsForType(type, options) {
    const incoming = (options && typeof options === 'object') ? structuredCloneQuestion(options) : {};
    const defaults = structuredCloneQuestion(getDefaultQuestion(type).options);

    const merged = { ...defaults, ...incoming };

    if ('choices' in defaults) {
        merged.choices = Array.isArray(incoming.choices) && incoming.choices.length
            ? incoming.choices.map((choice, index) => ({ ...defaultChoice(index + 1), ...choice }))
            : defaults.choices;
    }

    if ('emotions' in defaults) {
        merged.emotions = Array.isArray(incoming.emotions) && incoming.emotions.length
            ? incoming.emotions.map((emotion, index) => ({ ...defaultTableEmotion(index + 1), ...emotion }))
            : defaults.emotions;
    }

    if ('row_fields' in defaults) {
        merged.row_fields = Array.isArray(incoming.row_fields) && incoming.row_fields.length
            ? incoming.row_fields.map((field, index) => ({ ...defaultRowField(index + 1), ...field }))
            : defaults.row_fields;
    }

    return merged;
}

function cleanQuestionOptions(type, options) {
    return mergeOptionsForType(type, options);
}

function normalizeBoolean(value) {
    return value === true || value === '1' || value === 1 || value === 'true' || value === 'on';
}

function hiddenInput(name, value) {
    return `<input type="hidden" name="${escapeHtml(name)}" value="${escapeAttribute(value ?? '')}">`;
}

function structuredCloneQuestion(value) {
    return JSON.parse(JSON.stringify(value));
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escapeAttribute(value) {
    return escapeHtml(value).replace(/\n/g, '&#10;');
}

document.addEventListener('DOMContentLoaded', initReflectionTemplateBuilder);
