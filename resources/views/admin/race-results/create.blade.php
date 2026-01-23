@extends('admin.layouts.app')

@section('title', 'Создать результат')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Создать результат гонки</h2>
    <a href="{{ route('admin.race-results.pending') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
</div>

<div class="row">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Информация о результате</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.race-results.store') }}" method="POST" data-ajax>
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_profile_id" class="form-label">Профиль <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_profile_id') is-invalid @enderror" 
                                    id="user_profile_id" name="user_profile_id" required>
                                <option value="">-- Выберите профиль --</option>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}" 
                                            {{ old('user_profile_id') == $profile->id ? 'selected' : '' }}>
                                        {{ $profile->admin_full_name ?? 'Профиль #' . $profile->id }}
                                        @if($profile->user)
                                            - {{ $profile->user->email }}
                                        @endif
                                        @if($profile->ironman_number)
                                            (№{{ $profile->ironman_number }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_profile_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="race_date" class="form-label">Дата гонки <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('race_date') is-invalid @enderror" 
                                   id="race_date" 
                                   name="race_date" 
                                   value="{{ old('race_date') }}" 
                                   required>
                            @error('race_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Место проведения <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location') }}" 
                                   required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="race_type" class="form-label">Тип гонки <span class="text-danger">*</span></label>
                            <select class="form-select @error('race_type') is-invalid @enderror" 
                                    id="race_type" 
                                    name="race_type" 
                                    required>
                                <option value="">-- Выберите тип --</option>
                                @foreach($raceTypes as $type)
                                    <option value="{{ $type->value }}" 
                                            {{ old('race_type') == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('race_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Время (формат: ЧЧ:ММ:СС)</h5>

                    @php
                        // Функция для конвертации секунд в формат времени
                        function formatTimeForInput($seconds) {
                            if (!$seconds || $seconds == 0) return '';
                            if (is_string($seconds) && str_contains($seconds, ':')) return $seconds;
                            $hours = intdiv($seconds, 3600);
                            $minutes = intdiv($seconds % 3600, 60);
                            $secs = $seconds % 60;
                            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
                        }
                    @endphp

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="swim_time" class="form-label">Плавание</label>
                            <input type="text" 
                                   class="form-control time-input @error('swim_time') is-invalid @enderror" 
                                   id="swim_time" 
                                   data-time-field="swim_time_seconds"
                                   value="{{ formatTimeForInput(old('swim_time')) }}" 
                                   placeholder="00:00:00">
                            <input type="hidden" id="swim_time_seconds" name="swim_time" value="{{ is_numeric(old('swim_time')) ? old('swim_time') : '' }}">
                            @error('swim_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="t1_time" class="form-label">T1 (Переход 1)</label>
                            <input type="text" 
                                   class="form-control time-input @error('t1_time') is-invalid @enderror" 
                                   id="t1_time" 
                                   data-time-field="t1_time_seconds"
                                   value="{{ formatTimeForInput(old('t1_time')) }}" 
                                   placeholder="00:00:00">
                            <input type="hidden" id="t1_time_seconds" name="t1_time" value="{{ is_numeric(old('t1_time')) ? old('t1_time') : '' }}">
                            @error('t1_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="bike_time" class="form-label">Велосипед</label>
                            <input type="text" 
                                   class="form-control time-input @error('bike_time') is-invalid @enderror" 
                                   id="bike_time" 
                                   data-time-field="bike_time_seconds"
                                   value="{{ formatTimeForInput(old('bike_time')) }}" 
                                   placeholder="00:00:00">
                            <input type="hidden" id="bike_time_seconds" name="bike_time" value="{{ is_numeric(old('bike_time')) ? old('bike_time') : '' }}">
                            @error('bike_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="t2_time" class="form-label">T2 (Переход 2)</label>
                            <input type="text" 
                                   class="form-control time-input @error('t2_time') is-invalid @enderror" 
                                   id="t2_time" 
                                   data-time-field="t2_time_seconds"
                                   value="{{ formatTimeForInput(old('t2_time')) }}" 
                                   placeholder="00:00:00">
                            <input type="hidden" id="t2_time_seconds" name="t2_time" value="{{ is_numeric(old('t2_time')) ? old('t2_time') : '' }}">
                            @error('t2_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="run_time" class="form-label">Бег</label>
                            <input type="text" 
                                   class="form-control time-input @error('run_time') is-invalid @enderror" 
                                   id="run_time" 
                                   data-time-field="run_time_seconds"
                                   value="{{ formatTimeForInput(old('run_time')) }}" 
                                   placeholder="00:00:00">
                            <input type="hidden" id="run_time_seconds" name="run_time" value="{{ is_numeric(old('run_time')) ? old('run_time') : '' }}">
                            @error('run_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="total_time" class="form-label">Общее время <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control time-input @error('total_time') is-invalid @enderror" 
                                   id="total_time" 
                                   data-time-field="total_time_seconds"
                                   value="{{ formatTimeForInput(old('total_time')) }}" 
                                   required
                                   placeholder="00:00:00">
                            <input type="hidden" id="total_time_seconds" name="total_time" value="{{ is_numeric(old('total_time')) ? old('total_time') : '' }}">
                            @error('total_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Формат: ЧЧ:ММ:СС (например: 01:30:45)
                            </small>
                        </div>

                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="is_approved" 
                               name="is_approved" 
                               value="1"
                               {{ old('is_approved', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_approved">
                            Одобрить сразу (иначе результат будет ожидать одобрения)
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Создать результат
                        </button>
                        <a href="{{ route('admin.race-results.pending') }}" class="btn btn-secondary">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js" integrity="sha512-jV/g7Q3dEDFI9Tw6Ad+F2T6x7yajtib0AwfNMOzWrnRNryMGGAdVfPsjBnoOu3Q1vZ37LrPGg8KygD8DtJOiA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    // Инициализация масок для времени
    $(document).ready(function() {
        // Ждем загрузки inputmask
        function initTimeMasks() {
            try {
                if (typeof $.fn.inputmask !== 'undefined') {
                    $('.time-input').inputmask('99:99:99', {
                        placeholder: '00:00:00',
                        insertMode: false,
                        showMaskOnHover: false,
                        showMaskOnFocus: true,
                        clearMaskOnLostFocus: false
                    });
                    console.log('Inputmask initialized');
                } else {
                    console.warn('Inputmask not loaded, retrying...');
                    // Пробуем загрузить альтернативный CDN
                    $.getScript('https://cdn.jsdelivr.net/npm/inputmask@5.0.8/dist/inputmask.min.js')
                        .done(function() {
                            if (typeof $.fn.inputmask !== 'undefined') {
                                $('.time-input').inputmask('99:99:99', {
                                    placeholder: '00:00:00',
                                    insertMode: false,
                                    showMaskOnHover: false,
                                    showMaskOnFocus: true,
                                    clearMaskOnLostFocus: false
                                });
                                console.log('Inputmask loaded from alternative CDN');
                            }
                        });
                }
            } catch (e) {
                console.error('Error initializing inputmask:', e);
            }
        }
        
        // Инициализируем после небольшой задержки для гарантии загрузки
        setTimeout(initTimeMasks, 100);
    });

    // Конвертация времени из формата HH:MM:SS в секунды
    function timeToSeconds(timeString) {
        if (!timeString || timeString === '00:00:00' || timeString.trim() === '') {
            return 0;
        }
        
        const parts = timeString.split(':');
        if (parts.length !== 3) {
            return 0;
        }
        
        const hours = parseInt(parts[0]) || 0;
        const minutes = parseInt(parts[1]) || 0;
        const seconds = parseInt(parts[2]) || 0;
        
        return (hours * 3600) + (minutes * 60) + seconds;
    }

    // Конвертация секунд в формат HH:MM:SS
    function secondsToTime(totalSeconds) {
        if (!totalSeconds || totalSeconds === 0) {
            return '00:00:00';
        }
        
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        
        return String(hours).padStart(2, '0') + ':' + 
               String(minutes).padStart(2, '0') + ':' + 
               String(seconds).padStart(2, '0');
    }

    // Обновление скрытого поля со значением в секундах
    function updateTimeField(inputId) {
        const input = $('#' + inputId);
        const timeString = input.val();
        const seconds = timeToSeconds(timeString);
        const hiddenFieldId = input.data('time-field');
        
        if (hiddenFieldId) {
            $('#' + hiddenFieldId).val(seconds);
        }
    }

    // Автоматический расчет общего времени
    function calculateTotalTime() {
        const swim = timeToSeconds($('#swim_time').val());
        const t1 = timeToSeconds($('#t1_time').val());
        const bike = timeToSeconds($('#bike_time').val());
        const t2 = timeToSeconds($('#t2_time').val());
        const run = timeToSeconds($('#run_time').val());
        
        const total = swim + t1 + bike + t2 + run;
        
        if (total > 0) {
            $('#total_time').val(secondsToTime(total));
            $('#total_time_seconds').val(total);
        }
    }

    // Обновление скрытых полей при изменении времени
    $('.time-input').on('blur', function() {
        updateTimeField($(this).attr('id'));
        calculateTotalTime();
    });

    // Обновление при вводе для расчета общего времени
    $('#swim_time, #t1_time, #bike_time, #t2_time, #run_time').on('input', function() {
        updateTimeField($(this).attr('id'));
        calculateTotalTime();
    });

    // Перед отправкой формы убеждаемся, что все поля времени конвертированы
    $('form[data-ajax]').on('submit', function(e) {
        $('.time-input').each(function() {
            updateTimeField($(this).attr('id'));
        });
    });
</script>
@endpush
@endsection

