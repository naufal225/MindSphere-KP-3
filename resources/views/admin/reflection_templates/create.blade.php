@extends('components.admin.layout.app')

@section('title', 'Buat Template Refleksi')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Buat Template Refleksi</h1>
        <p class="text-gray-600">Susun pertanyaan refleksi dinamis untuk diisi siswa di aplikasi mobile.</p>
    </div>

    @include('admin.reflection_templates.partials.form', [
        'action' => route('admin.reflection-templates.store'),
        'method' => 'POST',
        'template' => null,
        'submitLabel' => 'Simpan Template',
    ])
</div>
@endsection
