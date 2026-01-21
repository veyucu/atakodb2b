@extends('layouts.app')

@section('title', 'Kullanıcı Düzenle - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit"></i> Kullanıcı Düzenle
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Kullanıcı Bilgileri</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Kullanıcı Kodu <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" name="username" value="{{ old('username', $user->username) }}"
                                        required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Şifre <small class="text-muted">(Değiştirmek
                                            istemiyorsanız boş bırakın)</small></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="user_type" class="form-label">Kullanıcı Tipi <span
                                        class="text-danger">*</span></label>
                                <select class="form-control @error('user_type') is-invalid @enderror" id="user_type"
                                    name="user_type" required>
                                    <option value="">Seçiniz...</option>
                                    <option value="admin" {{ old('user_type', $user->user_type) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="plasiyer" {{ old('user_type', $user->user_type) === 'plasiyer' ? 'selected' : '' }}>Plasiyer</option>
                                    <option value="musteri" {{ old('user_type', $user->user_type) === 'musteri' ? 'selected' : '' }}>Müşteri</option>
                                </select>
                                @error('user_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-building"></i> Ek Bilgiler (Opsiyonel)</h6>

                            <div class="mb-3">
                                <label for="adres" class="form-label">Adres</label>
                                <textarea class="form-control @error('adres') is-invalid @enderror" id="adres" name="adres"
                                    rows="2">{{ old('adres', $user->adres) }}</textarea>
                                @error('adres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="il" class="form-label">İl</label>
                                    <input type="text" class="form-control" id="il" name="il"
                                        value="{{ old('il', $user->il) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ilce" class="form-label">İlçe</label>
                                    <input type="text" class="form-control" id="ilce" name="ilce"
                                        value="{{ old('ilce', $user->ilce) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="plasiyer_kodu" class="form-label">Plasiyer Kodu</label>
                                    <input type="text" class="form-control" id="plasiyer_kodu" name="plasiyer_kodu"
                                        value="{{ old('plasiyer_kodu', $user->plasiyer_kodu) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Aktif
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> İptal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Güncelle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection