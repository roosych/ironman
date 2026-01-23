<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ-панель') - Ironman</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .main-content {
            min-height: calc(100vh - 56px);
        }
        .notification-container {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }
        .notification {
            margin-bottom: 10px;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-check"></i> Админ-панель
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-house"></i> Главная
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(request()->route()->getName() ?? '', 'admin.race-results.')) ? 'active' : '' }}" href="{{ route('admin.race-results.pending') }}">
                            <i class="bi bi-trophy"></i> Результаты
                        </a>
                    </li>
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-white" style="border: none; background: none;">
                                <i class="bi bi-box-arrow-right"></i> Выход
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Дашборд
                    </a>
                    <a href="{{ route('admin.race-results.pending') }}" class="list-group-item list-group-item-action {{ (str_starts_with(request()->route()->getName() ?? '', 'admin.race-results.')) ? 'active' : '' }}">
                        <i class="bi bi-trophy"></i> Результаты гонок
                    </a>
                    <a href="{{ route('admin.race-results.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-plus-circle"></i> Создать результат
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content p-4">
                <!-- Notifications Container -->
                <div class="notification-container" id="notification-container"></div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // CSRF Token для AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Функция для показа уведомлений
        function showNotification(message, type = 'success') {
            const container = $('#notification-container');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';
            
            const notification = $(`
                <div class="alert ${alertClass} alert-dismissible fade show notification" role="alert">
                    <i class="bi ${icon}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            container.append(notification);
            
            // Автоматически скрыть через 5 секунд
            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Обработка AJAX форм
        $(document).on('submit', 'form[data-ajax]', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            // Убираем предыдущие ошибки валидации
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();
            
            // Блокируем кнопку
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Загрузка...');
            
            const formData = new FormData(this);
            const url = form.attr('action');
            const method = form.attr('method') || 'POST';
            
            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        showNotification(response.message || 'Операция выполнена успешно', 'success');
                        if (response.reload) {
                            setTimeout(() => location.reload(), 1500);
                        }
                    }
                },
                error: function(xhr) {
                    let message = 'Произошла ошибка';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            message = errors.join('<br>');
                        }
                    } else if (xhr.status === 422) {
                        message = 'Ошибка валидации данных';
                    }
                    
                    showNotification(message, 'error');
                    
                    // Показываем ошибки валидации под полями
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.keys(xhr.responseJSON.errors).forEach(function(field) {
                            const input = form.find('[name="' + field + '"]');
                            if (input.length) {
                                input.addClass('is-invalid');
                                const errorDiv = $('<div class="invalid-feedback">' + xhr.responseJSON.errors[field][0] + '</div>');
                                input.after(errorDiv);
                            }
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Обработка AJAX кнопок
        $(document).on('click', 'button[data-ajax], a[data-ajax]', function(e) {
            e.preventDefault();
            
            const btn = $(this);
            const url = btn.data('url') || btn.attr('href');
            const method = btn.data('method') || 'POST';
            const confirmText = btn.data('confirm');
            
            if (confirmText && !confirm(confirmText)) {
                return;
            }
            
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            
            $.ajax({
                url: url,
                type: method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    showNotification(response.message || 'Операция выполнена успешно', 'success');
                    if (response.reload) {
                        setTimeout(() => location.reload(), 1500);
                    } else if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    let message = 'Произошла ошибка';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showNotification(message, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>

