<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $subject }}</title>
    <style>
        body {
            margin: 0;
            font-family: "Montserrat", sans-serif;
            background: #f6f8fc;
            color: #0f172a;
        }
        .email-shell {
            width: 100%;
            background: #f6f8fc;
            padding: 2rem 1rem;
        }
        .email-card {
            max-width: 640px;
            margin: 0 auto;
            background: #fff;
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
        }
        .email-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #2452e6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
        }
        .logo-circle img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            filter: invert(1);
        }
        .email-body {
            line-height: 1.7;
            font-size: 1rem;
            color: #475467;
        }
        .email-footer {
            margin-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }
        .email-footer a {
            color: #475467;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .email-footer a .bi {
            font-size: 1rem;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="email-shell">
        <div class="email-card">
            <div class="email-header">
                <div class="logo-circle">
                    <img src="{{ $logoUrl }}" alt="Nirmaan logo">
                </div>
                <h2 style="margin:0;">{{ config('branding.app_name', 'Nirmaan') }}</h2>
            </div>
            <div class="email-body">
                {!! $body !!}
            </div>
            <div class="email-footer">
                @foreach($socialLinks as $platform => $url)
                <a href="{{ $url }}" target="_blank" rel="noreferrer">
                    <i class="bi bi-{{ $platform === 'twitter' ? 'twitter' : $platform }} fs-5"></i>
                    {{ ucfirst($platform) }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
