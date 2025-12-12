<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Пароль успешно изменён - {{ config('app.name', 'Laravel') }}</title>
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
            max-width: 500px;
            width: 100%;
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        }
        .success-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.5rem;
            background-color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-icon svg {
            width: 32px;
            height: 32px;
            color: white;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            text-align: center;
        }
        p {
            color: #706f6c;
            text-align: center;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #1b1b18;
            color: white;
            text-decoration: none;
            border-radius: 0.25rem;
            font-weight: 500;
            text-align: center;
            width: 100%;
            transition: background-color 0.15s;
            margin-bottom: 0.75rem;
        }
        .btn:hover {
            background-color: #000;
        }
        .btn-secondary {
            background-color: transparent;
            color: #1b1b18;
            border: 1px solid #1b1b18;
        }
        .btn-secondary:hover {
            background-color: #f9fafb;
        }
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0a0a0a;
                color: #EDEDEC;
            }
            .container {
                background: #161615;
            }
            .btn-secondary {
                color: #EDEDEC;
                border-color: #3E3E3A;
            }
            .btn-secondary:hover {
                background-color: #1b1b18;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1>Пароль успешно изменён</h1>
        <p>Ваш пароль был успешно изменён. Теперь вы можете войти в систему, используя новый пароль.</p>

        <a href="{{ config('app.frontend_url', config('app.url')) }}/login" class="btn">
            Войти в аккаунт
        </a>
        <a href="{{ config('app.frontend_url', config('app.url')) }}" class="btn btn-secondary">
            Перейти на главную
        </a>
    </div>
</body>
</html>

