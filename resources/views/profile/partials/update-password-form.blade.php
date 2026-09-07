<section>
    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label small text-muted text-uppercase fw-bold">Password Saat Ini <span class="text-danger">*</span></label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" autocomplete="current-password" placeholder="Masukkan password saat ini" required />
            @if($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label small text-muted text-uppercase fw-bold">Password Baru <span class="text-danger">*</span></label>
            <input id="update_password_password" name="password" type="password" class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" autocomplete="new-password" placeholder="Minimal 8 karakter" required />
            @if($errors->updatePassword->has('password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label small text-muted text-uppercase fw-bold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" autocomplete="new-password" placeholder="Ulangi password baru" required />
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center mt-4">
            <button type="submit" class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold shadow-sm hover-scale">
                <i class="bi bi-shield-check me-2"></i>Perbarui Password
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success ms-3 fw-semibold small d-inline-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-1 fs-5 text-success"></i> Password berhasil diperbarui!
                </span>
            @endif
        </div>
    </form>
</section>
