<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Contractor Workspace - Nirmaan' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Montserrat", sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at 10% 0%, #ffffff 0%, #f3f7ff 48%, #e3ecff 100%);
            color: #0f172a;
        }

        .navbar-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(15, 47, 115, 0.12);
        }

        .brand-chip {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #2452e6;
            color: #fff;
            font-weight: 700;
        }

        .workspace-main {
            padding: 1.4rem 0 2rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    @if (session('success'))
    <div id="flash-message" class="position-fixed top-50 start-50 translate-middle" style="z-index: 2000;">
        <div class="alert alert-success shadow-lg mb-0" role="alert">
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if (session('error'))
    <div id="flash-error" class="position-fixed top-50 start-50 translate-middle" style="z-index: 2000;">
        <div class="alert alert-danger shadow-lg mb-0" role="alert">
            {{ session('error') }}
        </div>
    </div>
    @endif

    <nav class="navbar navbar-expand-lg navbar-light navbar-glass sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-inline-flex align-items-center gap-2" href="{{ route('contractor.projects') }}">
                <span class="brand-chip">N</span>
                <span>Contractor Workspace</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#contractorNavbar" aria-controls="contractorNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="contractorNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link {{ ($activePage ?? '') === 'projects' ? 'active fw-semibold' : '' }}" href="{{ route('contractor.projects') }}">Open Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($activePage ?? '') === 'bids' ? 'active fw-semibold' : '' }}" href="{{ route('contractor.bids') }}">My Bids</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($activePage ?? '') === 'awards' ? 'active fw-semibold' : '' }}" href="{{ route('contractor.awards') }}">Awarded</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($activePage ?? '') === 'messages' ? 'active fw-semibold' : '' }}" href="{{ route('contractor.messages') }}">Messages</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="workspace-main">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ['flash-message', 'flash-error'].forEach(function (id) {
                const flashElement = document.getElementById(id);
                if (!flashElement) {
                    return;
                }

                setTimeout(function () {
                    flashElement.classList.add('opacity-0');
                    flashElement.style.transition = 'opacity 0.6s ease';
                    setTimeout(function () {
                        flashElement.remove();
                    }, 700);
                }, 3000);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
