@extends('admin.layouts.app')

@section('title', 'Результаты на одобрение')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history"></i> Результаты на одобрение</h2>
    <a href="{{ route('admin.race-results.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Создать результат
    </a>
</div>

@if($results->count() > 0)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Профиль</th>
                            <th>Дата гонки</th>
                            <th>Место</th>
                            <th>Тип</th>
                            <th>Время</th>
                            <th>Создан</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr>
                                <td>{{ $result->id }}</td>
                                <td>
                                    @if($result->profile)
                                        <strong>{{ $result->profile->admin_full_name ?? 'Не указано' }}</strong>
                                        @if($result->profile->user)
                                            <br><small class="text-muted">{{ $result->profile->user->email }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Профиль не найден</span>
                                    @endif
                                </td>
                                <td>{{ $result->race_date->format('d.m.Y') }}</td>
                                <td>
                                    {{ $result->location }}
                                    @if($result->overall_position)
                                        <br><small class="text-muted">Место: {{ $result->overall_position }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $result->race_type->label() }}</span>
                                </td>
                                <td>
                                    <strong>{{ \App\Models\RaceResult::formatTime($result->total_time) }}</strong>
                                    <br><small class="text-muted">
                                        Плавание: {{ $result->swim_time ? \App\Models\RaceResult::formatTime($result->swim_time) : '-' }} |
                                        T1: {{ $result->t1_time ? \App\Models\RaceResult::formatTime($result->t1_time) : '-' }} |
                                        Велосипед: {{ $result->bike_time ? \App\Models\RaceResult::formatTime($result->bike_time) : '-' }} |
                                        T2: {{ $result->t2_time ? \App\Models\RaceResult::formatTime($result->t2_time) : '-' }} |
                                        Бег: {{ $result->run_time ? \App\Models\RaceResult::formatTime($result->run_time) : '-' }}
                                    </small>
                                </td>
                                <td>{{ $result->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-success" 
                                                data-ajax 
                                                data-url="{{ route('admin.race-results.approve', $result) }}"
                                                data-method="POST"
                                                title="Одобрить">
                                            <i class="bi bi-check-circle"></i> Одобрить
                                        </button>
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                data-ajax 
                                                data-url="{{ route('admin.race-results.reject', $result) }}"
                                                data-method="POST"
                                                data-confirm="Вы уверены, что хотите отклонить этот результат?"
                                                title="Отклонить">
                                            <i class="bi bi-x-circle"></i> Отклонить
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $results->links() }}
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Нет результатов, ожидающих одобрения
    </div>
@endif
@endsection

