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
        .logo-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 0.5rem;
            background: #2452e6;
            box-shadow: 0 12px 30px rgba(36, 82, 230, 0.35);
        }
        .logo-letter {
            color: #fff;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 32px;
            line-height: 1;
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
        .email-footer .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f3f4f6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #2452e6;
            font-size: 1.1rem;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }
        .email-footer a {
            text-decoration: none;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="email-shell">
        <div class="email-card">
            <div class="email-header">
            <div class="logo-badge">
                <span class="logo-letter">{{ strtoupper(substr(config('branding.app_name', 'Nirmaan'), 0, 1)) }}</span>
            </div>
            <h2 style="margin:0;">{{ config('branding.app_name', 'Nirmaan') }}</h2>
            </div>
            <div class="email-body">
                {!! $body !!}
            </div>
            <div class="email-footer">
            @php
                $iconMap = [
                    'facebook' => 'facebook',
                    'x' => 'x-lg',
                    'instagram' => 'instagram',
                    'linkedin' => 'linkedin',
                ];
            @endphp
            @foreach($socialLinks as $platform => $url)
            <a href="{{ $url }}" target="_blank" rel="noreferrer" aria-label="{{ ucfirst($platform) }}">
                <span class="social-icon">
                    <i class="bi bi-{{ $iconMap[$platform] ?? 'link-45deg' }}"></i>
                </span>
            </a>
            @endforeach
        </div>
    </div>
    </div>
</body>
</html>
