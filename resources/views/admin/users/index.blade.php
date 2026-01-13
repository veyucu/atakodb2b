@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-users"></i> Kullanıcı Yönetimi
        </h2>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Kullanıcı Ekle
            </a>
        </div>
    </div>

    @if($users->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kullanıcı Kodu</th>
                                <th>Ad Soyad</th>
                                <th>E-posta</th>
                                <th>Kullanıcı Tipi</th>
                                <th>İl/İlçe</th>
                                <th style="width: 100px;">Durum</th>
                                <th style="width: 150px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->user_type === 'admin')
                                            <span class="badge bg-danger">Admin</span>
                                        @elseif($user->user_type === 'plasiyer')
                                            <span class="badge bg-info">Plasiyer</span>
                                        @else
                                            <span class="badge bg-primary">Müşteri</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->il ? $user->il . '/' . $user->ilce : '-' }}</td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
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

        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> Henüz kullanıcı eklenmemiş.
            <a href="{{ route('admin.users.create') }}" class="alert-link">İlk kullanıcıyı ekleyin.</a>
        </div>
    @endif
</div>
@endsection





