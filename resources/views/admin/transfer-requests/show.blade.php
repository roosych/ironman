@extends('admin.layouts.app')

@section('title', 'Детали запроса трансфера #' . $transferRequest->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="bi bi-arrow-left-right"></i>
        Запрос трансфера #{{ $transferRequest->id }}
    </h1>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.transfer-requests.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Назад к списку
        </a>

        @if($transferRequest->status === 'pending')
            <button type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#approveModal">
                <i class="bi bi-check-circle"></i> Одобрить
            </button>

            <button type="button"
                    class="btn btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectModal">
                <i class="bi bi-x-circle"></i> Отклонить
            </button>
        @endif
    </div>
</div>

<!-- Статус запроса -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1">
                            @if($transferRequest->status === 'pending')
                                <span class="badge bg-warning text-dark fs-6">
                                    <i class="bi bi-clock-history"></i> В ожидании рассмотрения
                                </span>
                            @elseif($transferRequest->status === 'approved')
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle"></i> Одобрено
                                </span>
                            @elseif($transferRequest->status === 'rejected')
                                <span class="badge bg-danger fs-6">
                                    <i class="bi bi-x-circle"></i> Отклонено
                                </span>
                            @endif
                        </h5>
                        <div class="text-muted">
                            <i class="bi bi-calendar"></i> Создан: {{ $transferRequest->created_at->format('d.m.Y в H:i') }}
                            @if($transferRequest->reviewed_at)
                                <br>
                                <i class="bi bi-person-check"></i> Рассмотрен: {{ $transferRequest->reviewed_at->format('d.m.Y в H:i') }}
                                @if($transferRequest->reviewedBy)
                                    ({{ $transferRequest->reviewedBy->name }})
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($transferRequest->status === 'pending')
                            <span class="text-warning">
                                <i class="bi bi-exclamation-triangle fs-2"></i>
                            </span>
                        @elseif($transferRequest->status === 'approved')
                            <span class="text-success">
                                <i class="bi bi-check-circle fs-2"></i>
                            </span>
                        @elseif($transferRequest->status === 'rejected')
                            <span class="text-danger">
                                <i class="bi bi-x-circle fs-2"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Информация о пользователе -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle text-primary"></i>
                    Пользователь-заявитель
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <i class="bi bi-person fs-1 text-muted me-3"></i>
                    <div>
                        <h6 class="fw-bold">{{ $transferRequest->user->name }}</h6>
                        <div class="text-muted mb-2">{{ $transferRequest->user->email }}</div>

                        @if($transferRequest->user->profile)
                            <div class="small text-muted">
                                <div><strong>Профиль:</strong> {{ $transferRequest->user->profile->role ?? 'Не указано' }}</div>
                                @if($transferRequest->user->profile->country_iso)
                                    <div><strong>Страна:</strong> {{ $transferRequest->user->profile->country_iso }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Информация об атлете-доноре -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-award text-warning"></i>
                    Атлет-донор
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <i class="bi bi-trophy fs-1 text-muted me-3"></i>
                    <div>
                        <h6 class="fw-bold">{{ $transferRequest->sourceAthlete->admin_full_name }}</h6>

                        <div class="small">
                            @if($transferRequest->sourceAthlete->ironman_number)
                                <div class="mb-1">
                                    <span class="badge bg-primary">IM#{{ $transferRequest->sourceAthlete->ironman_number }}</span>
                                </div>
                            @endif

                            @if($transferRequest->sourceAthlete->country_iso)
                                <div class="text-muted">
                                    <i class="bi bi-geo-alt"></i> {{ $transferRequest->sourceAthlete->country_iso }}
                                </div>
                            @endif
                        </div>

                        <!-- Статистика результатов по типам -->
                        <div class="mt-3">
                            <h6 class="small text-muted mb-2">Количество результатов:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @if($raceCounts->get('ironman', 0) > 0)
                                    <span class="badge bg-success">Ironman: {{ $raceCounts->get('ironman', 0) }}</span>
                                @endif
                                @if($raceCounts->get('ironman_70_3', 0) > 0)
                                    <span class="badge bg-info">70.3: {{ $raceCounts->get('ironman_70_3', 0) }}</span>
                                @endif
                                @if($raceCounts->get('5150', 0) > 0)
                                    <span class="badge bg-warning">5150: {{ $raceCounts->get('5150', 0) }}</span>
                                @endif
                            </div>
                            <div class="small text-muted mt-1">
                                Всего результатов: {{ $transferRequest->sourceAthlete->raceResults->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Результаты атлета-донора -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list-check text-info"></i>
            Результаты для трансфера ({{ $transferRequest->sourceAthlete->raceResults->count() }})
        </h5>
    </div>
    <div class="card-body p-0">
        @if($transferRequest->sourceAthlete->raceResults->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Дата</th>
                            <th>Локация</th>
                            <th>Тип гонки</th>
                            <th>Общее время</th>
                            <th class="text-center">Детали</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transferRequest->sourceAthlete->raceResults->sortByDesc('race_date') as $result)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $result->race_date->format('d.m.Y') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt text-muted me-1"></i>
                                        {{ $result->location }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge
                                        @if($result->race_type->value === 'ironman') bg-success
                                        @elseif($result->race_type->value === 'ironman_70_3') bg-info
                                        @elseif($result->race_type->value === '5150') bg-warning text-dark
                                        @endif">
                                        {{ $result->race_type->label() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        {{ $result->total_time ? \App\Models\RaceResult::formatTime($result->total_time) : 'DNS/DNF' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-info"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#result-{{ $result->id }}"
                                            title="Показать детали">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="result-{{ $result->id }}">
                                <td colspan="5" class="bg-light">
                                    <div class="row g-3 p-3">
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="text-muted small">Плавание</div>
                                                <div class="fw-semibold">{{ \App\Models\RaceResult::formatTime($result->swim_time ?? 0) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="text-muted small">T1</div>
                                                <div class="fw-semibold">{{ \App\Models\RaceResult::formatTime($result->t1_time ?? 0) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="text-muted small">Велосипед</div>
                                                <div class="fw-semibold">{{ \App\Models\RaceResult::formatTime($result->bike_time ?? 0) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="text-muted small">T2</div>
                                                <div class="fw-semibold">{{ \App\Models\RaceResult::formatTime($result->t2_time ?? 0) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="text-muted small">Бег</div>
                                                <div class="fw-semibold">{{ \App\Models\RaceResult::formatTime($result->run_time ?? 0) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                @if($result->overall_position)
                                                    <div class="text-muted small">Общее место</div>
                                                    <div class="fw-semibold">#{{ $result->overall_position }}</div>
                                                @endif
                                                @if($result->age_group_position)
                                                    <div class="text-muted small">Место в АГ</div>
                                                    <div class="fw-semibold">#{{ $result->age_group_position }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <div class="mt-3">
                    <h6>Нет результатов</h6>
                    <p>У атлета-донора нет результатов для трансфера.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Комментарии и история -->
@if($transferRequest->comment || $transferRequest->admin_comment)
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-chat-dots text-secondary"></i>
            Комментарии
        </h5>
    </div>
    <div class="card-body">
        @if($transferRequest->comment)
            <div class="mb-3">
                <h6 class="text-primary">
                    <i class="bi bi-person"></i> Комментарий пользователя:
                </h6>
                <div class="p-3 bg-light rounded">
                    {{ $transferRequest->comment }}
                </div>
                <small class="text-muted">
                    <i class="bi bi-clock"></i> {{ $transferRequest->created_at->format('d.m.Y в H:i') }}
                </small>
            </div>
        @endif

        @if($transferRequest->admin_comment)
            <div class="mb-3">
                <h6 class="text-info">
                    <i class="bi bi-shield-check"></i> Комментарий администратора:
                </h6>
                <div class="p-3 bg-light rounded border-start border-info border-3">
                    {{ $transferRequest->admin_comment }}
                </div>
                <small class="text-muted">
                    <i class="bi bi-clock"></i>
                    @if($transferRequest->reviewedBy)
                        {{ $transferRequest->reviewedBy->name }},
                    @endif
                    {{ $transferRequest->reviewed_at->format('d.m.Y в H:i') }}
                </small>
            </div>
        @endif
    </div>
</div>
@endif

<!-- Modal: Одобрить запрос -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle text-success"></i> Одобрить запрос
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form data-ajax method="POST" action="{{ route('admin.transfer-requests.approve', $transferRequest->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Вы собираетесь одобрить запрос пользователя <strong>{{ $transferRequest->user->name }}</strong>
                        на перенос результатов от атлета <strong>{{ $transferRequest->sourceAthlete->admin_full_name }}</strong>.
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Внимание:</strong> После одобрения все результаты атлета-донора будут переданы пользователю
                        и станут недоступны для других операций трансфера.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Комментарий (необязательно):</label>
                        <textarea name="comment"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Дополнительная информация по решению..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Отмена
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2"></i> Одобрить запрос
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Отклонить запрос -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle text-danger"></i> Отклонить запрос
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form data-ajax method="POST" action="{{ route('admin.transfer-requests.reject', $transferRequest->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Вы собираетесь отклонить запрос пользователя <strong>{{ $transferRequest->user->name }}</strong>
                        на перенос результатов от атлета <strong>{{ $transferRequest->sourceAthlete->admin_full_name }}</strong>.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Причина отклонения <span class="text-danger">*</span>:</label>
                        <textarea name="comment"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Укажите причину отклонения запроса..."
                                  required></textarea>
                        <div class="form-text">Обязательное поле для отклонения запроса</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Отмена
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Отклонить запрос
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection