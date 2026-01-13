@extends('layouts.app')

@section('title', 'Slider Yönetimi - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-images"></i> Slider Yönetimi
        </h2>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Slider Ekle
            </a>
        </div>
    </div>

    @if($sliders->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Resim</th>
                                <th>Başlık</th>
                                <th>Açıklama</th>
                                <th style="width: 80px;">Sıra</th>
                                <th style="width: 100px;">Durum</th>
                                <th style="width: 150px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sliders as $slider)
                                <tr>
                                    <td>
                                        <img src="{{ $slider->image_url }}" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 50px; object-fit: cover;"
                                             alt="{{ $slider->title }}">
                                    </td>
                                    <td>{{ $slider->title ?? '-' }}</td>
                                    <td>{{ Str::limit($slider->description, 50) ?? '-' }}</td>
                                    <td>{{ $slider->order }}</td>
                                    <td>
                                        @if($slider->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.sliders.edit', $slider) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sliders.destroy', $slider) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bu slider\'ı silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> Henüz slider eklenmemiş.
            <a href="{{ route('admin.sliders.create') }}" class="alert-link">İlk slider'ı ekleyin.</a>
        </div>
    @endif
</div>
@endsection


