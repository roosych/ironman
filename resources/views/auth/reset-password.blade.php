<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Сброс пароля - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #FDFDFC;
            color: #1b1b18;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 450px;
            width: 100%;
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .subtitle {
            color: #706f6c;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            font-size: 1rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #1b1b18;
            box-shadow: 0 0 0 3px rgba(27, 27, 24, 0.1);
        }
        input.error {
            border-color: #ef4444;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .btn {
            width: 100%;
            padding: 0.75rem 1.5rem;
            background-color: #1b1b18;
            color: white;
            border: none;
            border-radius: 0.25rem;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.15s;
            margin-top: 0.5rem;
        }
        .btn:hover {
            background-color: #000;
        }
        .btn:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
        }
        .loading {
            display: none;
            text-align: center;
            margin-top: 1rem;
            color: #706f6c;
        }
        .loading.show {
            display: block;
        }
        .success-message {
            display: none;
            padding: 0.75rem;
            background-color: #d1fae5;
            color: #065f46;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .success-message.show {
            display: block;
        }
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0a0a0a;
                color: #EDEDEC;
            }
            .container {
                background: #161615;
            }
            input {
                background-color: #1b1b18;
                border-color: #3E3E3A;
                color: #EDEDEC;
            }
            input:focus {
                border-color: #EDEDEC;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Сброс пароля</h1>
        <p class="subtitle">Введите новый пароль для вашего аккаунта</p>

        <form id="resetPasswordForm">
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="password">Новый пароль</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    minlength="8"
                >
                <div class="error-message" id="passwordError"></div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Подтвердите пароль</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    minlength="8"
                >
                <div class="error-message" id="passwordConfirmationError"></div>
            </div>

            <div class="success-message" id="successMessage"></div>

            <button type="submit" class="btn" id="submitBtn">
                Сбросить пароль
            </button>

            <div class="loading" id="loading">
                Отправка...
            </div>
        </form>
    </div>

    <script>
        document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            const successMessage = document.getElementById('successMessage');
            
            // Reset errors
            document.querySelectorAll('.error-message').forEach(el => {
                el.classList.remove('show');
                el.textContent = '';
            });
            document.querySelectorAll('input').forEach(el => {
                el.classList.remove('error');
            });
            successMessage.classList.remove('show');

            // Disable form
            submitBtn.disabled = true;
            loading.classList.add('show');

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('/api/v1/auth/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Success - redirect to success page
                    window.location.href = '/reset-password/success';
                } else {
                    // Show errors
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const input = document.querySelector(`[name="${field}"]`);
                            const errorDiv = document.getElementById(field + 'Error') || 
                                           document.getElementById('passwordConfirmationError');
                            
                            if (input) {
                                input.classList.add('error');
                            }
                            if (errorDiv) {
                                errorDiv.textContent = result.errors[field][0];
                                errorDiv.classList.add('show');
                            }
                        });
                    } else if (result.message) {
                        successMessage.textContent = result.message;
                        successMessage.classList.add('show');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                const errorDiv = document.getElementById('passwordError');
                errorDiv.textContent = 'Произошла ошибка. Пожалуйста, попробуйте снова.';
                errorDiv.classList.add('show');
            } finally {
                submitBtn.disabled = false;
                loading.classList.remove('show');
            }
        });
    </script>
</body>
</html>

