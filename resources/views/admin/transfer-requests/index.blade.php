@extends('admin.layouts.app')

@section('title', 'Трансфер результатов')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-arrow-left-right"></i> Трансфер результатов</h1>

    <!-- Статистика -->
    <div class="card-deck d-flex gap-2">
        <div class="card bg-warning text-white" style="min-width: 120px;">
            <div class="card-body p-2 text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-clock-history fs-4 me-2"></i>
                    <div>
                        <div class="fs-5 fw-bold">{{ $stats['pending'] }}</div>
                        <div class="small">В ожидании</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-success text-white" style="min-width: 120px;">
            <div class="card-body p-2 text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-check-circle fs-4 me-2"></i>
                    <div>
                        <div class="fs-5 fw-bold">{{ $stats['approved'] }}</div>
                        <div class="small">Одобрено</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-danger text-white" style="min-width: 120px;">
            <div class="card-body p-2 text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-x-circle fs-4 me-2"></i>
                    <div>
                        <div class="fs-5 fw-bold">{{ $stats['rejected'] }}</div>
                        <div class="small">Отклонено</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Фильтры -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.transfer-requests.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Статус:</label>
                <select name="status" class="form-select">
                    <option value="">Все статусы</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>В ожидании</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Одобрено</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Отклонено</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">На странице:</label>
                <select name="per_page" class="form-select">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel"></i> Фильтровать
                </button>
                <a href="{{ route('admin.transfer-requests.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Сбросить
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Список запросов -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list-ul"></i>
            Запросы на трансфер ({{ $requests->total() }})
        </h5>
    </div>
    <div class="card-body p-0">
        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Пользователь</th>
                            <th>Атлет-донор</th>
                            <th>Результаты</th>
                            <th>Статус</th>
                            <th>Дата создания</th>
                            <th class="text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $request->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle fs-5 text-muted me-2"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $request->user->name }}</div>
                                            <div class="text-muted small">{{ $request->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-award fs-5 text-primary me-2"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $request->sourceAthlete->admin_full_name }}</div>
                                            <div class="text-muted small">
                                                @if($request->sourceAthlete->ironman_number)
                                                    IM#{{ $request->sourceAthlete->ironman_number }}
                                                @endif
                                                {{ $request->sourceAthlete->country_iso }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $request->sourceAthlete->raceResults->count() }} результатов
                                    </span>
                                </td>
                                <td>
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock-history"></i> В ожидании
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Одобрено
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Отклонено
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted">
                                        {{ $request->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.transfer-requests.show', $request->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Просмотр деталей">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($request->status === 'pending')
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#approveModal"
                                                    data-request-id="{{ $request->id }}"
                                                    data-user-name="{{ $request->user->name }}"
                                                    data-athlete-name="{{ $request->sourceAthlete->admin_full_name }}"
                                                    title="Одобрить">
                                                <i class="bi bi-check2"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal"
                                                    data-request-id="{{ $request->id }}"
                                                    data-user-name="{{ $request->user->name }}"
                                                    data-athlete-name="{{ $request->sourceAthlete->admin_full_name }}"
                                                    title="Отклонить">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <div class="mt-3 text-muted">
                    <h5>Запросы не найдены</h5>
                    <p>Нет запросов соответствующих выбранным фильтрам.</p>
                </div>
            </div>
        @endif
    </div>

    @if($requests->hasPages())
        <div class="card-footer">
            {{ $requests->withQueryString()->links() }}
        </div>
    @endif
</div>

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
            <form data-ajax method="POST" id="approveForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Вы собираетесь одобрить запрос пользователя <strong id="approveUserName"></strong>
                        на перенос результатов от атлета <strong id="approveAthleteName"></strong>.
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
                        <i class="bi bi-check2"></i> Одобрить
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
            <form data-ajax method="POST" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Вы собираетесь отклонить запрос пользователя <strong id="rejectUserName"></strong>
                        на перенос результатов от атлета <strong id="rejectAthleteName"></strong>.
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
                        <i class="bi bi-x-circle"></i> Отклонить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Обработка модального окна одобрения
    $('#approveModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const requestId = button.data('request-id');
        const userName = button.data('user-name');
        const athleteName = button.data('athlete-name');

        const modal = $(this);
        modal.find('#approveUserName').text(userName);
        modal.find('#approveAthleteName').text(athleteName);
        modal.find('#approveForm').attr('action', `/admin/transfer-requests/${requestId}/approve`);
    });

    // Обработка модального окна отклонения
    $('#rejectModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const requestId = button.data('request-id');
        const userName = button.data('user-name');
        const athleteName = button.data('athlete-name');

        const modal = $(this);
        modal.find('#rejectUserName').text(userName);
        modal.find('#rejectAthleteName').text(athleteName);
        modal.find('#rejectForm').attr('action', `/admin/transfer-requests/${requestId}/reject`);
    });
});
</script>
@endpush