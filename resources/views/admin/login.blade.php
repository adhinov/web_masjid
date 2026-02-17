@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="text-masjid fw-semibold mb-3 text-center">Login Admin</h2>
                    <p class="text-muted text-center mb-4">Masuk untuk mengelola konten masjid.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small-text">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.submit') }}" class="admin-login-form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control input-soft input-compact" placeholder="Masukan Email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="admin-password" class="form-control input-soft input-compact pe-5" placeholder="Masukan Password" required>
                                <button type="button" class="btn btn-link password-toggle" aria-label="Toggle password visibility">
                                    <span class="eye-icon">👁</span>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-admin w-100">Masuk</button>
                    </form>

                    <p class="text-muted small-text text-center mt-3 mb-0">
                        Lupa password? Hubungi pengurus.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.querySelector(".password-toggle");
    const input = document.getElementById("admin-password");
    if (!toggle || !input) return;

    toggle.addEventListener("click", function () {
        const isPassword = input.type === "password";
        input.type = isPassword ? "text" : "password";
        toggle.classList.toggle("is-active", isPassword);
    });
});
</script>
@endsection
