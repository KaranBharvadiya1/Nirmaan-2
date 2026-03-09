<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Contractor Panel - Nirmaan' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #edf3ff;
            --bg-soft: #f8fbff;
            --text-main: #0f172a;
            --text-muted: #566079;
            --accent: #2452e6;
            --sidebar-bg: linear-gradient(160deg, #0a1f4f 0%, #0f2f73 55%, #12439f 100%);
            --sidebar-text: #c6d5ff;
            --sidebar-active-bg: #ffffff;
            --sidebar-active-text: #0f2f73;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Montserrat", sans-serif;
            color: var(--text-main);
            background: radial-gradient(circle at 15% 0%, #ffffff 0%, var(--bg-main) 45%, #dde8ff 100%);
        }

        .mobile-topbar {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(15, 47, 115, 0.12);
        }

        .brand-chip {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--accent);
            color: #fff;
            font-weight: 700;
        }

        .dashboard-layout {
            min-height: 100vh;
        }

        .dashboard-sidebar {
            background: var(--sidebar-bg);
            color: #fff;
            padding: 1.4rem 1rem;
        }

        .sidebar-brand {
            text-decoration: none;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            font-weight: 700;
            padding: 0.4rem 0.5rem;
            margin-bottom: 1.2rem;
        }

        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .sidebar-menu-title {
            color: rgba(255, 255, 255, 0.72);
            letter-spacing: 0.06em;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            margin: 1.15rem 0 0.65rem;
            padding-left: 0.55rem;
        }

        .sidebar-link {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.8rem;
            border-radius: 0.8rem;
            color: var(--sidebar-text);
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.14);
        }

        .sidebar-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            box-shadow: 0 10px 24px rgba(7, 20, 53, 0.26);
        }

        .sidebar-link i {
            font-size: 1.05rem;
            width: 1.3rem;
            text-align: center;
        }

        .sidebar-link-content {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.7rem;
            flex: 1;
        }

        .sidebar-notification-badge {
            min-width: 1.45rem;
            height: 1.45rem;
            padding: 0 0.35rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffcf6b;
            color: #10264f;
            font-size: 0.72rem;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(16, 38, 79, 0.18);
        }

        .sidebar-user {
            margin-top: auto;
            border-radius: 1rem;
            padding: 0.9rem;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .sidebar-user .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.24);
        }

        .avatar-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.38);
        }

        .dashboard-main {
            padding: 1.1rem 1rem 1.75rem;
        }

        @media (min-width: 992px) {
            .dashboard-layout {
                display: grid;
                grid-template-columns: 290px minmax(0, 1fr);
            }

            .dashboard-sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
            }

            .dashboard-main {
                padding: 1.7rem 2rem 2.1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $authUser = auth()->user();
        $fullName = trim(($authUser->first_name ?? '').' '.($authUser->last_name ?? ''));
        $initial = strtoupper(substr($authUser->first_name ?? 'C', 0, 1));
        $profileImageUrl = $authUser?->profile_image_url;
        $menuBadgeCount = static function (string $key) use ($layoutNotificationCounts): int {
            return match ($key) {
                'bids' => (int) ($layoutNotificationCounts['bids'] ?? 0),
                default => 0,
            };
        };

        $menuSections = [
            [
                'title' => 'Workspace',
                'items' => [
                    ['key' => 'projects', 'icon' => 'bi-search', 'label' => 'Open Projects', 'url' => route('contractor.projects')],
                    ['key' => 'bids', 'icon' => 'bi-receipt-cutoff', 'label' => 'My Bids', 'url' => route('contractor.bids')],
                    ['key' => 'awards', 'icon' => 'bi-trophy', 'label' => 'Awarded', 'url' => route('contractor.awards')],
                    ['key' => 'portfolio', 'icon' => 'bi-images', 'label' => 'Portfolio', 'url' => route('contractor.portfolio')],
                ],
            ],
            [
                'title' => 'Communication',
                'items' => [
                    ['key' => 'messages', 'icon' => 'bi-chat-left-dots', 'label' => 'Messages', 'url' => route('contractor.messages')],
                    ['key' => 'settings', 'icon' => 'bi-gear', 'label' => 'Settings', 'url' => route('contractor.settings')],
                ],
            ],
        ];
    @endphp

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

    <header class="mobile-topbar sticky-top d-lg-none">
        <div class="container-fluid py-2 d-flex align-items-center justify-content-between">
            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#contractorSidebarMobile" aria-controls="contractorSidebarMobile">
                <i class="bi bi-list"></i>
            </button>
            <a class="text-decoration-none fw-bold text-primary d-inline-flex align-items-center gap-2" href="{{ route('contractor.projects') }}">
                <span class="brand-chip">N</span>
                <span>Contractor Panel</span>
            </a>
            @if ($profileImageUrl)
            <img src="{{ $profileImageUrl }}" alt="Contractor profile image" class="avatar-image">
            @else
            <span class="badge text-bg-light border">{{ $initial }}</span>
            @endif
        </div>
    </header>

    <div class="offcanvas offcanvas-start text-white" tabindex="-1" id="contractorSidebarMobile" aria-labelledby="contractorSidebarMobileLabel" style="background: var(--sidebar-bg);">
        <div class="offcanvas-header border-bottom border-light border-opacity-25">
            <h2 class="offcanvas-title h6 mb-0" id="contractorSidebarMobileLabel">Nirmaan Contractor</h2>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <nav>
                @foreach ($menuSections as $section)
                <p class="sidebar-menu-title {{ $loop->first ? 'mt-0' : '' }}">{{ $section['title'] }}</p>
                @foreach ($section['items'] as $item)
                @php $badgeCount = $menuBadgeCount($item['key']); @endphp
                <a class="sidebar-link {{ ($activePage ?? '') === $item['key'] ? 'active' : '' }}" href="{{ $item['url'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span class="sidebar-link-content">
                        <span>{{ $item['label'] }}</span>
                        <span
                            class="sidebar-notification-badge {{ $badgeCount > 0 ? '' : 'd-none' }}"
                            @if ($item['key'] === 'messages') data-message-badge @endif
                        >{{ $badgeCount }}</span>
                    </span>
                </a>
                @endforeach
                @endforeach
            </nav>
            <div class="sidebar-user mt-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if ($profileImageUrl)
                    <img src="{{ $profileImageUrl }}" alt="Contractor profile image" class="avatar-image">
                    @else
                    <span class="avatar">{{ $initial }}</span>
                    @endif
                    <div>
                        <p class="mb-0 fw-semibold small">{{ $fullName ?: 'Contractor User' }}</p>
                        <p class="mb-0 small text-white-50">{{ $authUser->email ?? '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm w-100 fw-semibold">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="dashboard-layout">
        <aside class="dashboard-sidebar d-none d-lg-flex flex-column">
            <a class="sidebar-brand" href="{{ route('contractor.projects') }}">
                <span class="sidebar-brand-icon">N</span>
                <span>Contractor Workspace</span>
            </a>

            <nav>
                @foreach ($menuSections as $section)
                <p class="sidebar-menu-title">{{ $section['title'] }}</p>
                @foreach ($section['items'] as $item)
                @php $badgeCount = $menuBadgeCount($item['key']); @endphp
                <a class="sidebar-link {{ ($activePage ?? '') === $item['key'] ? 'active' : '' }}" href="{{ $item['url'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span class="sidebar-link-content">
                        <span>{{ $item['label'] }}</span>
                        <span
                            class="sidebar-notification-badge {{ $badgeCount > 0 ? '' : 'd-none' }}"
                            @if ($item['key'] === 'messages') data-message-badge @endif
                        >{{ $badgeCount }}</span>
                    </span>
                </a>
                @endforeach
                @endforeach
            </nav>

            <div class="sidebar-user">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if ($profileImageUrl)
                    <img src="{{ $profileImageUrl }}" alt="Contractor profile image" class="avatar-image">
                    @else
                    <span class="avatar">{{ $initial }}</span>
                    @endif
                    <div>
                        <p class="mb-0 fw-semibold small">{{ $fullName ?: 'Contractor User' }}</p>
                        <p class="mb-0 small text-white-50">{{ $authUser->email ?? '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm w-100 fw-semibold">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="dashboard-main">
            @yield('content')
        </main>
    </div>

    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h2 class="modal-title h5 mb-0" id="confirmActionModalLabel">Please Confirm</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="confirmActionMessage">Are you sure you want to continue?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmActionSubmitBtn">Yes, Continue</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flashElements = ['flash-message', 'flash-error']
                .map(function (id) {
                    return document.getElementById(id);
                })
                .filter(Boolean);

            flashElements.forEach(function (flashElement) {
                setTimeout(function () {
                    flashElement.classList.add('opacity-0');
                    flashElement.style.transition = 'opacity 0.6s ease';
                    setTimeout(function () {
                        flashElement.remove();
                    }, 700);
                }, 3000);
            });

            const confirmModalElement = document.getElementById('confirmActionModal');
            const confirmMessageElement = document.getElementById('confirmActionMessage');
            const confirmSubmitButton = document.getElementById('confirmActionSubmitBtn');
            const confirmModal = confirmModalElement ? bootstrap.Modal.getOrCreateInstance(confirmModalElement) : null;
            let pendingForm = null;

            document.querySelectorAll('form.js-confirm-submit').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.dataset.confirmed === 'true') {
                        form.dataset.confirmed = 'false';
                        return;
                    }

                    event.preventDefault();

                    const customMessage = form.dataset.confirmMessage || 'Are you sure you want to continue?';
                    if (confirmMessageElement) {
                        confirmMessageElement.textContent = customMessage;
                    }

                    pendingForm = form;
                    if (confirmModal) {
                        confirmModal.show();
                    }
                });
            });

            if (confirmSubmitButton) {
                confirmSubmitButton.addEventListener('click', function () {
                    if (!pendingForm) {
                        return;
                    }

                    pendingForm.dataset.confirmed = 'true';
                    pendingForm.requestSubmit();
                    pendingForm = null;

                    if (confirmModal) {
                        confirmModal.hide();
                    }
                });
            }

            if (confirmModalElement) {
                confirmModalElement.addEventListener('hidden.bs.modal', function () {
                    pendingForm = null;
                });
            }
        });
    </script>
    @include('shared.messaging.layout-badge-script')
    @stack('scripts')
</body>
</html>
