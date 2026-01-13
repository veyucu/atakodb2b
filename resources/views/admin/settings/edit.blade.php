@extends('layouts.app')

@section('title', 'Site Ayarları - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-cog"></i> Site Ayarları
                    </h5>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site İsmi <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="site_name"
                                   name="site_name"
                                   value="{{ old('site_name', $setting?->site_name ?? config('app.name')) }}"
                                   class="form-control @error('site_name') is-invalid @enderror"
                                   required>
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_logo" class="form-label">Site Logosu</label>
                            @if($setting?->logo_url)
                                <div class="mb-2">
                                    <img src="{{ $setting->logo_url }}"
                                         alt="Site Logosu"
                                         style="max-height: 80px;"
                                         class="img-thumbnail">
                                </div>
                            @endif
                            <input type="file"
                                   id="site_logo"
                                   name="site_logo"
                                   accept="image/*"
                                   class="form-control @error('site_logo') is-invalid @enderror">
                            <small class="text-muted">PNG önerilir. Maksimum 4MB.</small>
                            @error('site_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


