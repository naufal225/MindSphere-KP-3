@extends('components.admin.layout.app')

@section('title', 'Edit Template Refleksi')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Template Refleksi</h1>
        <p class="text-gray-600">Perbarui detail template, pertanyaan, dan assignment global.</p>
    </div>

    @include('admin.reflection_templates.partials.form', [
        'action' => route('admin.reflection-templates.update', $template->id),
        'method' => 'PUT',
        'template' => $template,
        'submitLabel' => 'Perbarui Template',
    ])
</div>
@endsection
