@extends('layouts.app')

@section('title', 'Tambah Jadwal Khotib')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h2 class="text-masjid fw-semibold mb-3">Tambah Jadwal Khotib</h2>

            <form method="POST" action="{{ route('admin.khotib-schedules.store') }}">
                @include('admin.khotib-schedules._form', ['submitLabel' => 'Simpan'])
            </form>
        </div>
    </div>
</div>
@endsection
