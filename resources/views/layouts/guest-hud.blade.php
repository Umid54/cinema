<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Вход')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background:
                radial-gradient(900px 400px at 20% -10%, rgba(34,211,238,.35), transparent 45%),
                radial-gradient(700px 300px at 80% 10%, rgba(52,211,153,.30), transparent 45%),
                #020617;
            color: #e5e7eb; /* 🔴 КЛЮЧЕВО */
            font-family: system-ui, -apple-system, BlinkMacSystemFont;
        }

        .hud-card {
            background: rgba(2, 6, 23, 0.92);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(148,163,184,.25);
            box-shadow:
                0 0 0 1px rgba(34,211,238,.25),
                0 0 45px rgba(34,211,238,.45);
        }

        .hud-label {
            color: #e5e7eb;
            font-size: 0.875rem;
        }

        .hud-input {
            background: rgba(15,23,42,0.9);
            border: 1px solid rgba(148,163,184,.4);
            color: #f8fafc;
        }

        .hud-input::placeholder {
            color: #94a3b8;
        }

        .hud-input:focus {
            outline: none;
            border-color: #22d3ee;
            box-shadow: 0 0 0 1px rgba(34,211,238,.6);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">

@yield('content')

</body>
</html>
м