@extends('components.admin.layout.app')

@section('header', 'Edit Reward')
@section('subtitle', 'Perbarui reward yang sudah ada')

@section('content')

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div
                class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg">
                <i class="text-xl text-white fa-solid fa-gift"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Reward</h1>
                <p class="text-gray-600">Perbarui informasi reward</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center mb-2">
            <i class="mr-2 text-red-500 fa-solid fa-circle-exclamation"></i>
            <h3 class="text-sm font-semibold text-red-800">Terjadi kesalahan:</h3>
        </div>
        <ul class="ml-6 text-sm text-red-700 list-disc">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.rewards.update', $reward->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <!-- Nama Reward -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="mr-1 fa-solid fa-signature"></i> Nama Reward *
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $reward->name) }}"
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh: Voucher Belanja Rp 50.000" required>
                    <p class="mt-1 text-xs text-gray-500">Nama reward yang akan ditampilkan</p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="mr-1 fa-solid fa-align-left"></i> Deskripsi
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Deskripsi detail tentang reward">{{ old('description', $reward->description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Deskripsi opsional untuk reward</p>
                </div>

                <!-- Tipe Reward -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="mr-1 fa-solid fa-tag"></i> Tipe Reward *
                    </label>
                    <select id="type" name="type"
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">Pilih Tipe Reward</option>
                        <option value="physical" {{ old('type', $reward->type) == 'physical' ? 'selected' : '' }}>Fisik (Hadiah fisik,
                            perlu pengambilan)</option>
                        <option value="digital" {{ old('type', $reward->type) == 'digital' ? 'selected' : '' }}>Digital (Hadiah digital,
                            dikirim via kode)</option>
                        <option value="voucher" {{ old('type', $reward->type) == 'voucher' ? 'selected' : '' }}>Voucher (Kode voucher
                            untuk merchant)</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Tentukan tipe reward</p>
                </div>

                <!-- Biaya Koin -->
                <div>
                    <label for="coin_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="mr-1 fa-solid fa-coins"></i> Biaya Koin *
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="text-yellow-500 fa-solid fa-coins"></i>
                        </div>
                        <input type="number" id="coin_cost" name="coin_cost" value="{{ old('coin_cost', $reward->coin_cost) }}"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="500" min="1" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Jumlah koin yang dibutuhkan untuk menukar reward ini</p>
                </div>

                <!-- Stok -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="mr-1 fa-solid fa-boxes"></i> Stok *
                    </label>
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="text-gray-500 fa-solid fa-cube"></i>
                                </div>
                                <input type="number" id="stock" name="stock"
                                    value="{{ old('stock', $reward->stock == -1 ? '' : $reward->stock) }}"
                                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="999" min="1">
                            </div>
                        </div>
                        <div>
                            <button type="button" id="unlimited-toggle"
                                class="px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors {{ $reward->stock == -1 ? 'bg-green-100 text-green-700 border-green-300' : '' }}">
                                @if($reward->stock == -1)
                                    <i class="mr-2 fa-solid fa-check"></i> Unlimited
                                @else
                                    <i class="mr-2 fa-solid fa-infinity"></i> Unlimited
                                @endif
                            </button>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Jumlah stok yang tersedia</p>
                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                </div>

                <!-- Masa Berlaku (Conditional) -->
                <div id="validity-section" class="{{ in_array(old('type', $reward->type), ['digital', 'voucher']) ? '' : 'hidden' }}">
                    <label for="validity_days" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="mr-1 fa-solid fa-clock"></i> Masa Berlaku (hari)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="text-gray-500 fa-solid fa-calendar-day"></i>
                        </div>
                        <input type="number" id="validity_days" name="validity_days" value="{{ old('validity_days', $reward->validity_days) }}"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="30" min="1">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Masa berlaku reward setelah ditukar (hanya untuk digital/voucher)</p>
                </div>

                <!-- Gambar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="mr-1 fa-solid fa-image"></i> Gambar Reward
                    </label>

                    <!-- Image Preview -->
                    <div class="mb-4">
                        <div id="image-preview" class="{{ $reward->image_url ? '' : 'hidden' }}">
                            <div class="relative inline-block">
                                @if($reward->image_url)
                                    @if(str_starts_with($reward->image_url, 'http'))
                                        <img id="preview-image" src="{{ $reward->image_url }}"
                                            class="w-48 h-48 object-cover rounded-lg border border-gray-200 shadow-sm">
                                    @else
                                        <img id="preview-image" src="{{ ($reward->image_url) }}"
                                            class="w-48 h-48 object-cover rounded-lg border border-gray-200 shadow-sm">
                                    @endif
                                @else
                                    <img id="preview-image" src=""
                                        class="w-48 h-48 object-cover rounded-lg border border-gray-200 shadow-sm">
                                @endif
                                <button type="button" id="remove-image"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                    <i class="fa-solid fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div id="no-image" class="{{ !$reward->image_url ? 'flex' : 'hidden' }} flex-col items-center justify-center w-48 h-48 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                            <i class="text-4xl text-gray-400 fa-solid fa-image"></i>
                            <p class="mt-2 text-sm text-gray-500">Preview gambar</p>
                        </div>
                    </div>

                    <!-- Upload Options -->
                    <div class="space-y-4">
                        <!-- Upload File -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                                Upload Gambar Baru
                            </label>
                            <input type="file" id="image" name="image" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</p>
                        </div>

                        <!-- OR Divider -->
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">ATAU</span>
                            </div>
                        </div>

                        <!-- URL Input -->
                        <div>
                            <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">
                                URL Gambar
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="text-gray-500 fa-solid fa-link"></i>
                                </div>
                                <input type="url" id="image_url" name="image_url" value="{{ old('image_url', $reward->image_url) }}"
                                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="https://example.com/image.jpg">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Masukkan URL gambar eksternal jika ada</p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-toggle-on"></i> Status Aktif
                        </label>
                        <div class="relative inline-block w-12 mr-2 align-middle select-none">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                {{ old('is_active', $reward->is_active) ? 'checked' : '' }}>
                            <label for="is_active"
                                class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Reward akan langsung tersedia jika diaktifkan</p>
                </div>

                <!-- Additional Info (Advanced) -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center mb-4">
                        <i class="mr-2 text-gray-500 fa-solid fa-gear"></i>
                        <h3 class="text-lg font-medium text-gray-900">Info Tambahan (Opsional)</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kondisi Reward
                            </label>
                            <input type="text" name="additional_info[condition]"
                                value="{{ old('additional_info.condition', $reward->additional_info['condition'] ?? '') }}"
                                class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: Baru, Original">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Brand/Merk
                            </label>
                            <input type="text" name="additional_info[brand]"
                                value="{{ old('additional_info.brand', $reward->additional_info['brand'] ?? '') }}"
                                class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: Nike, Apple">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Catatan Tambahan
                            </label>
                            <textarea name="additional_info[notes]" rows="2"
                                class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Catatan khusus tentang reward ini">{{ old('additional_info.notes', $reward->additional_info['notes'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500">
                            <i class="mr-1 fa-solid fa-circle-info"></i>
                            Field dengan tanda (*) wajib diisi
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.rewards.index') }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="mr-2 fa-solid fa-times"></i> Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="mr-2 fa-solid fa-check"></i> Perbarui Reward
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle unlimited stock dengan hidden input yang lebih aman
        const unlimitedToggle = document.getElementById('unlimited-toggle');
        const stockInput = document.getElementById('stock');
        const unlimitedHiddenInput = document.createElement('input');
        unlimitedHiddenInput.type = 'hidden';
        unlimitedHiddenInput.name = 'stock_unlimited';
        unlimitedHiddenInput.value = '{{ $reward->stock == -1 ? "1" : "0" }}';
        unlimitedHiddenInput.id = 'stock_unlimited';
        stockInput.parentNode.appendChild(unlimitedHiddenInput);

        // Set initial state based on current reward
        const isInitiallyUnlimited = {{ $reward->stock == -1 ? 'true' : 'false' }};
        if (isInitiallyUnlimited) {
            stockInput.disabled = true;
            stockInput.value = '';
            unlimitedToggle.innerHTML = '<i class="mr-2 fa-solid fa-check"></i> Unlimited';
            unlimitedToggle.classList.remove('border-gray-300', 'hover:bg-gray-50');
            unlimitedToggle.classList.add('bg-green-100', 'text-green-700', 'border-green-300');

            // Simpan nilai stock sebelumnya jika ada
            if (stockInput.getAttribute('value')) {
                stockInput.setAttribute('data-last-value', stockInput.getAttribute('value'));
            }
        }

        unlimitedToggle.addEventListener('click', function() {
            const isUnlimited = unlimitedHiddenInput.value === '1';

            if (isUnlimited) {
                // Ubah kembali ke limited
                unlimitedHiddenInput.value = '0';
                stockInput.disabled = false;
                stockInput.value = stockInput.getAttribute('data-last-value') || '1';
                stockInput.focus();
                unlimitedToggle.innerHTML = '<i class="mr-2 fa-solid fa-infinity"></i> Unlimited';
                unlimitedToggle.classList.remove('bg-green-100', 'text-green-700', 'border-green-300');
                unlimitedToggle.classList.add('border-gray-300', 'hover:bg-gray-50');
            } else {
                // Ubah ke unlimited
                if (stockInput.value) {
                    stockInput.setAttribute('data-last-value', stockInput.value);
                }
                unlimitedHiddenInput.value = '1';
                stockInput.value = '';
                stockInput.disabled = true;
                unlimitedToggle.innerHTML = '<i class="mr-2 fa-solid fa-check"></i> Unlimited';
                unlimitedToggle.classList.remove('border-gray-300', 'hover:bg-gray-50');
                unlimitedToggle.classList.add('bg-green-100', 'text-green-700', 'border-green-300');
            }
        });

        // Handle form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            if (unlimitedHiddenInput.value === '1') {
                // Jika unlimited, set nilai stock ke -1
                stockInput.value = '-1';
            }
        });

        // Show/hide validity days based on reward type
        const typeSelect = document.getElementById('type');
        const validitySection = document.getElementById('validity-section');

        function toggleValiditySection() {
            const selectedType = typeSelect.value;
            if (selectedType === 'digital' || selectedType === 'voucher') {
                validitySection.classList.remove('hidden');
            } else {
                validitySection.classList.add('hidden');
            }
        }

        typeSelect.addEventListener('change', toggleValiditySection);
        toggleValiditySection(); // Initial check

        // Image preview
        const imageInput = document.getElementById('image');
        const imageUrlInput = document.getElementById('image_url');
        const imagePreview = document.getElementById('image-preview');
        const noImage = document.getElementById('no-image');
        const previewImage = document.getElementById('preview-image');
        const removeImageBtn = document.getElementById('remove-image');
        const removeImageHidden = document.getElementById('remove_image');

        // Preview from file upload
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    noImage.classList.add('hidden');
                    removeImageHidden.value = '0'; // Reset remove flag
                }
                reader.readAsDataURL(file);
                // Clear URL input
                imageUrlInput.value = '';
            }
        });

        // Preview from URL
        imageUrlInput.addEventListener('input', function() {
            const url = this.value;
            if (url && isValidUrl(url)) {
                previewImage.src = url;
                imagePreview.classList.remove('hidden');
                noImage.classList.add('hidden');
                removeImageHidden.value = '0'; // Reset remove flag
                // Clear file input
                imageInput.value = '';
            }
        });

        // Remove image
        removeImageBtn.addEventListener('click', function() {
            imagePreview.classList.add('hidden');
            noImage.classList.remove('hidden');
            imageInput.value = '';
            imageUrlInput.value = '';
            removeImageHidden.value = '1'; // Set remove flag
            previewImage.src = '';
        });

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        // Toggle switch styling
        const toggleCheckbox = document.getElementById('is_active');
        const toggleLabel = document.querySelector('.toggle-label');

        function updateToggle() {
            if (toggleCheckbox.checked) {
                toggleLabel.classList.remove('bg-gray-300');
                toggleLabel.classList.add('bg-green-500');
            } else {
                toggleLabel.classList.remove('bg-green-500');
                toggleLabel.classList.add('bg-gray-300');
            }
        }

        toggleCheckbox.addEventListener('change', updateToggle);
        updateToggle(); // Initial update
    });
</script>

<style>
    /* Toggle switch styles */
    .toggle-checkbox:checked {
        right: 0;
        border-color: #10B981;
    }

    .toggle-checkbox:checked+.toggle-label {
        background-color: #10B981;
    }

    .toggle-checkbox {
        transition: all 0.3s;
        top: 0;
        right: 1.5rem;
    }

    .toggle-label {
        transition: all 0.3s;
    }

    /* Image preview styles */
    #preview-image {
        max-width: 100%;
        max-height: 300px;
        object-fit: cover;
    }
</style>
@endpush
