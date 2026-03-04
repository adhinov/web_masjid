@csrf

<div class="mb-3">
    <label class="form-label fw-semibold">Nama Khotib</label>
    <input type="text" name="khotib_name" class="form-control" value="{{ old('khotib_name', $schedule->khotib_name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Bilal</label>
    <input type="text" name="bilal" class="form-control" value="{{ old('bilal', $schedule->bilal ?? 'Bp. Adi') }}" required>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Tanggal Khutbah (YYYY-MM-DD)</label>
    <textarea name="khutbah_dates" rows="6" class="form-control" placeholder="Contoh:
2026-01-02
2026-04-03">{{ old('khutbah_dates', isset($schedule) ? implode("\n", $schedule->khutbah_dates ?? []) : '') }}</textarea>
    @error('khutbah_dates')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Keterangan</label>
    <input type="text" name="notes" class="form-control" value="{{ old('notes', $schedule->notes ?? '') }}">
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submitLabel }}</button>
    <a href="{{ route('admin.khotib-schedules.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
