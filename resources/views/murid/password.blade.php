@extends('layouts.murid')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding-top: 20px;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 800; color: #1e293b; letter-spacing: -0.5px;">Keamanan Akun</h1>
        <p style="font-size: 15px; color: #64748b; margin-top: 8px;">Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.</p>
    </div>

    @if (session('status') === 'password-updated')
        <div style="background: #ecfdf5; color: #065f46; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 600; border-left: 4px solid #10b981;">
            ✅ Kata sandi Anda telah berhasil diperbarui.
        </div>
    @endif

    <div style="background: #fff; border-radius: 20px; border: 1.5px solid #e2e8f0; padding: 40px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <!-- Current Password -->
            <div style="margin-bottom: 24px;">
                <label for="current_password" style="display: block; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Kata Sandi Saat Ini</label>
                <input id="current_password" name="current_password" type="password" style="width: 100%; padding: 14px 18px; border-radius: 12px; border: 2px solid #e2e8f0; font-size: 15px; outline: none; transition: 0.2s;" autocomplete="current-password">
                @if($errors->updatePassword->has('current_password'))
                    <p style="color: #ef4444; font-size: 13px; margin-top: 8px; font-weight: 500;">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <!-- New Password -->
            <div style="margin-bottom: 24px;">
                <label for="password" style="display: block; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Kata Sandi Baru</label>
                <input id="password" name="password" type="password" style="width: 100%; padding: 14px 18px; border-radius: 12px; border: 2px solid #e2e8f0; font-size: 15px; outline: none; transition: 0.2s;" autocomplete="new-password">
                @if($errors->updatePassword->has('password'))
                    <p style="color: #ef4444; font-size: 13px; margin-top: 8px; font-weight: 500;">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div style="margin-bottom: 32px;">
                <label for="password_confirmation" style="display: block; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Konfirmasi Kata Sandi Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" style="width: 100%; padding: 14px 18px; border-radius: 12px; border: 2px solid #e2e8f0; font-size: 15px; outline: none; transition: 0.2s;" autocomplete="new-password">
            </div>

            <div style="display: flex; gap: 12px; align-items: center;">
                <button type="submit" style="background: #111827; color: #fff; padding: 14px 28px; border-radius: 12px; font-size: 15px; font-weight: 700; border: none; cursor: pointer; transition: 0.2s;">
                    Simpan Perubahan
                </button>
                <a href="{{ route('murid.dashboard') }}" style="color: #64748b; font-size: 14px; font-weight: 600; text-decoration: none; padding: 10px 16px;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
    }
</style>
@endsection
