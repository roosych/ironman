@extends('admin.layouts.app')

@section('title', 'Дашборд')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Дашборд</h2>
</div>

<div class="row g-4">
    <!-- Статистика -->
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Ожидают одобрения</h6>
                        <h2 class="mb-0">{{ $stats['pending_race_results'] }}</h2>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.race-results.pending') }}" class="text-white text-decoration-none">
                    Просмотреть <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Всего профилей</h6>
                        <h2 class="mb-0">{{ $stats['total_profiles'] }}</h2>
                    </div>
                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Всего пользователей</h6>
                        <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                    </div>
                    <i class="bi bi-person-badge" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Быстрые действия</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('admin.race-results.pending') }}" class="btn btn-outline-warning w-100">
                            <i class="bi bi-clock-history"></i> Одобрить результаты
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('admin.race-results.create') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-plus-circle"></i> Создать результат
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

