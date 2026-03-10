<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Nirmaan connects builders and contractors to manage construction projects efficiently with real-time tracking, resource management, and seamless communication.">
    <meta name="keywords" content="construction, contractor, builder, site management, project tracking, real-time updates, resource management">
    <meta name="author" content="Nirmaan">
    <title>Nirmaan - Construction Site Management Platform</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Montserrat", sans-serif;
            scroll-behavior: smooth;
        }
        .auth-branding {
            text-align: center;
            margin-bottom: 1rem;
        }
        .auth-logo {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2452e6, #4d7cff);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.25rem;
            box-shadow: 0 10px 30px rgba(36, 82, 230, 0.4);
        }
        .auth-socials {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin-top: 1.25rem;
        }
        .auth-socials a {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 1px solid rgba(37, 99, 235, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #2452e6;
            background: #fff;
            transition: all 0.2s ease;
        }
        .auth-socials a:hover {
            border-color: #2452e6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(36, 82, 230, 0.3);
        }

        .navbar-glass {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(8px);
        }

        .hero-visual {
            max-width: 480px;
            width: 100%;
            height: auto;
        }

        .section-title {
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .service-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
        }
    </style>
</head>
<body class="bg-white">
    @php
        $contactErrors = $errors->getBag('contact');
        $signupErrors = $errors->getBag('signup');
        $loginErrors = $errors->getBag('login');

        $autoAuthForm = null;
        if (session('showSignup') || $signupErrors->any()) {
            $autoAuthForm = 'signup';
        } elseif (session('showLogin') || $loginErrors->any()) {
            $autoAuthForm = 'login';
        }
    @endphp

    @if($autoAuthForm)
    <script>
        window.__SHOW_AUTH_MODAL__ = @json($autoAuthForm);
    </script>
    @endif

    @if (session('success'))
    <div id="flash-message" class="position-fixed top-50 start-50 translate-middle" style="z-index: 2000;">
        <div class="alert alert-success shadow-lg mb-0" role="alert">
            {{ session('success') }}
        </div>
    </div>
    @endif

    <nav class="navbar navbar-expand-lg navbar-light navbar-glass fixed-top border-bottom shadow-sm">
        <div class="container py-2">
            <a class="navbar-brand fw-bold fs-3 text-primary d-flex align-items-center gap-2" href="/">
                <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:44px;height:44px;">N</span>
                <span class="fst-italic text-dark">irmaan</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-2">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
                <button type="button" class="btn btn-primary px-4" onclick="showAuthForm('login')">Get Started</button>
            </div>
        </div>
    </nav>

    <main class="pt-5 mt-4">
        <section id="home" class="py-5" itemscope itemtype="https://schema.org/Organization">
            <div class="container py-lg-5">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7">
                        <h1 class="display-4 fw-bold section-title mb-4" itemprop="name">
                            Welcome to <span class="text-primary" itemprop="brand">Nirmaan</span>!
                        </h1>
                        <p class="lead text-secondary mb-4" itemprop="description">
                            We are connecting builders, contractors, and teams to streamline construction projects, enhance collaboration, and bring efficiency to every stage of development.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <button type="button" class="btn btn-primary btn-lg px-4" onclick="showAuthForm('login')" itemprop="potentialAction">Get Started</button>
                            <a href="#about" class="btn btn-outline-secondary btn-lg px-4">Learn More</a>
                        </div>
                    </div>
                    <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                        <img class="hero-visual" loading="lazy" width="500" height="400" src="{{ url('images/construction-of-real-estate-vector.jpg') }}" alt="Illustration of construction professionals collaborating on real estate development projects with Nirmaan" itemprop="image">
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="py-5 bg-light">
            <div class="container py-lg-4">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-6">
                                <img class="img-fluid rounded-3 shadow-sm" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/content/office-long-2.png" alt="Modern office space 1">
                            </div>
                            <div class="col-6 mt-4 mt-lg-5">
                                <img class="img-fluid rounded-3 shadow-sm" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/content/office-long-1.png" alt="Modern office space 2">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h4 class="text-primary fw-semibold text-uppercase mb-3">Who We Are</h4>
                        <h2 class="section-title mb-3">Efficient Construction Site Management Made Simple</h2>
                        <p class="text-secondary mb-4">
                            At <strong>Nirmaan</strong>, we provide a smart and efficient way to manage construction projects. From tracking materials and labor to ensuring real-time project updates, our platform simplifies site management for builders, contractors, and project managers.
                        </p>

                        <ul class="nav nav-pills mb-3" id="missionVisionTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="mission-tab" data-bs-toggle="pill" data-bs-target="#mission-content" type="button" role="tab" aria-controls="mission-content" aria-selected="true">Our Mission</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="vision-tab" data-bs-toggle="pill" data-bs-target="#vision-content" type="button" role="tab" aria-controls="vision-content" aria-selected="false">Our Vision</button>
                            </li>
                        </ul>
                        <div class="tab-content bg-white border rounded-3 p-4 shadow-sm" id="missionVisionContent">
                            <div class="tab-pane fade show active" id="mission-content" role="tabpanel" aria-labelledby="mission-tab" tabindex="0">
                                <p class="text-secondary mb-0">
                                    To revolutionize the construction industry by seamlessly connecting project owners with trusted contractors, ensuring transparency, efficiency, and quality in every project.
                                </p>
                            </div>
                            <div class="tab-pane fade" id="vision-content" role="tabpanel" aria-labelledby="vision-tab" tabindex="0">
                                <p class="text-secondary mb-0">
                                    To become the leading digital platform that empowers construction professionals, streamlines project execution, and fosters innovation in the construction sector worldwide.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="py-5">
            <div class="container py-lg-4">
                <header class="text-center mb-5">
                    <h2 class="section-title">Efficient Construction Site Management with <span class="text-primary">Nirmaan</span></h2>
                    <p class="text-secondary mt-3 mx-auto" style="max-width:760px;">
                        Streamline project planning, resource allocation, and site coordination with Nirmaan - the ultimate construction management solution.
                    </p>
                </header>

                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <article class="card h-100 border-0 shadow-sm service-card text-center p-3">
                            <div class="card-body">
                                <img src="{{ url('images/real-time.webp') }}" alt="Illustration showing real-time tracking of construction project progress" loading="lazy" class="mb-4">
                                <h3 class="h5 fw-semibold">Real-Time Project Tracking</h3>
                                <p class="text-secondary mb-0">
                                    Monitor progress, track deadlines, and manage tasks efficiently with live updates and reports.
                                </p>
                            </div>
                        </article>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <article class="card h-100 border-0 shadow-sm service-card text-center p-3">
                            <div class="card-body">
                                <img src="{{ url('images/resourse.webp') }}" alt="Resource management icon showing optimization of materials and labor" loading="lazy" class="mb-4">
                                <h3 class="h5 fw-semibold">Resource Management</h3>
                                <p class="text-secondary mb-0">
                                    Optimize labor, materials, and equipment allocation to reduce waste and increase efficiency.
                                </p>
                            </div>
                        </article>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <article class="card h-100 border-0 shadow-sm service-card text-center p-3">
                            <div class="card-body">
                                <img src="{{ url('images/communication.webp') }}" alt="Communication icon representing seamless connection among team members" loading="lazy" class="mb-4">
                                <h3 class="h5 fw-semibold">Seamless Communication</h3>
                                <p class="text-secondary mb-0">
                                    Keep stakeholders, workers, and managers connected with instant updates and notifications.
                                </p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="py-5 bg-light">
            <div class="container py-lg-4">
                <header class="mb-5">
                    <p class="text-primary fw-semibold mb-2">Contact Us</p>
                    <h2 class="section-title h1 mb-3">Chat with our friendly team</h2>
                    <p class="text-secondary mb-0">We would love to hear from you. Please fill out this form or send us an email.</p>
                </header>

                <div class="row g-4 g-lg-5">
                    <div class="col-lg-6">
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="h-100 p-4 bg-white rounded-3 shadow-sm">
                                    <span class="contact-icon bg-primary text-white mb-3"><i class="bi bi-envelope-fill"></i></span>
                                    <h3 class="h6 fw-semibold mb-2">Email</h3>
                                    <p class="text-secondary small mb-1">Our friendly team is here to help.</p>
                                    <a class="text-decoration-none" href="mailto:hello@nirmaan.com">hello@nirmaan.com</a>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="h-100 p-4 bg-white rounded-3 shadow-sm">
                                    <span class="contact-icon bg-primary text-white mb-3"><i class="bi bi-chat-dots-fill"></i></span>
                                    <h3 class="h6 fw-semibold mb-2">Live Chat</h3>
                                    <p class="text-secondary small mb-1">Reach out anytime.</p>
                                    <a class="text-decoration-none" href="#">Start new chat</a>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="h-100 p-4 bg-white rounded-3 shadow-sm">
                                    <span class="contact-icon bg-primary text-white mb-3"><i class="bi bi-geo-alt-fill"></i></span>
                                    <h3 class="h6 fw-semibold mb-2">Office</h3>
                                    <p class="text-secondary small mb-0">100 Smith Street, Collingwood, VIC 3066</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="h-100 p-4 bg-white rounded-3 shadow-sm">
                                    <span class="contact-icon bg-primary text-white mb-3"><i class="bi bi-telephone-fill"></i></span>
                                    <h3 class="h6 fw-semibold mb-2">Phone</h3>
                                    <p class="text-secondary small mb-1">Mon-Fri from 8am to 5pm.</p>
                                    <a class="text-decoration-none" href="tel:+919876543210">+91 98765 43210</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="bg-white rounded-3 shadow-sm p-4 p-lg-5">
                            <form method="POST" action="{{ route('contact.submit') }}">
                                @csrf
                                @if($contactErrors->any())
                                <div class="alert alert-danger mb-3" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach($contactErrors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" class="form-control{{ $contactErrors->has('first_name') ? ' is-invalid' : '' }}" required>
                                        @if($contactErrors->has('first_name'))
                                        <div class="invalid-feedback">{{ $contactErrors->first('first_name') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" class="form-control{{ $contactErrors->has('last_name') ? ' is-invalid' : '' }}" required>
                                        @if($contactErrors->has('last_name'))
                                        <div class="invalid-feedback">{{ $contactErrors->first('last_name') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control{{ $contactErrors->has('email') ? ' is-invalid' : '' }}" required>
                                        @if($contactErrors->has('email'))
                                        <div class="invalid-feedback">{{ $contactErrors->first('email') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea id="message" name="message" rows="5" class="form-control{{ $contactErrors->has('message') ? ' is-invalid' : '' }}">{{ old('message') }}</textarea>
                                        @if($contactErrors->has('message'))
                                        <div class="invalid-feedback">{{ $contactErrors->first('message') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100 py-2">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-light pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <a class="text-decoration-none text-light fw-bold fs-3 d-flex align-items-center gap-2 mb-3" href="/">
                        <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;">N</span>
                        <span class="fst-italic">irmaan</span>
                    </a>
                    <p class="text-light-emphasis mb-3">
                        Streamlining construction projects with innovative digital solutions for builders, contractors, and project managers.
                    </p>
                    <div class="d-flex gap-3">
                        <a class="text-light-emphasis fs-5" href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a class="text-light-emphasis fs-5" href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a class="text-light-emphasis fs-5" href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h3 class="h6 text-uppercase mb-3">Quick Links</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a class="text-light-emphasis text-decoration-none" href="#home">Home</a></li>
                        <li class="mb-2"><a class="text-light-emphasis text-decoration-none" href="#about">About Us</a></li>
                        <li class="mb-2"><a class="text-light-emphasis text-decoration-none" href="#services">Services</a></li>
                        <li class="mb-2"><a class="text-light-emphasis text-decoration-none" href="#contact">Contact</a></li>
                        <li><a class="text-light-emphasis text-decoration-none" href="#">Pricing</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h3 class="h6 text-uppercase mb-3">Services</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a class="text-light-emphasis text-decoration-none" href="#">Project Tracking</a></li>
                        <li class="mb-2"><a class="text-light-emphasis text-decoration-none" href="#">Resource Management</a></li>
                        <li class="mb-2"><a class="text-light-emphasis text-decoration-none" href="#">Team Collaboration</a></li>
                        <li class="mb-2"><a class="text-light-emphasis text-decoration-none" href="#">Budget Control</a></li>
                        <li><a class="text-light-emphasis text-decoration-none" href="#">Quality Assurance</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h3 class="h6 text-uppercase mb-3">Contact Info</h3>
                    <p class="text-light-emphasis mb-2"><i class="bi bi-geo-alt-fill me-2 text-primary"></i>100 Construction Plaza, Mumbai, MH 400001</p>
                    <p class="text-light-emphasis mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i>+91 98765 43210</p>
                    <p class="text-light-emphasis mb-0"><i class="bi bi-envelope-fill me-2 text-primary"></i>hello@nirmaan.com</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center border-top border-secondary mt-4 pt-3">
                <p class="mb-2 mb-md-0 text-light-emphasis small">&copy; 2026 Nirmaan. All rights reserved.</p>
                <div class="d-flex gap-3 small">
                    <a class="text-light-emphasis text-decoration-none" href="#">Privacy Policy</a>
                    <a class="text-light-emphasis text-decoration-none" href="#">Terms of Service</a>
                    <a class="text-light-emphasis text-decoration-none" href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h2 class="modal-title h5 mb-0 text-primary" id="authModalLabel">Welcome to Nirmaan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="signupForm" action="{{ route('signup.submit') }}" method="POST" class="d-none">
                        @csrf
                        <h3 class="h4 fw-bold text-primary mb-3">Create Account</h3>
                        @if($signupErrors->any())
                        <div class="alert alert-danger mb-3" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach($signupErrors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="mb-3">
                            <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required class="form-control{{ $signupErrors->has('first_name') ? ' is-invalid' : '' }}">
                            @if($signupErrors->has('first_name'))
                            <div class="invalid-feedback">{{ $signupErrors->first('first_name') }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required class="form-control{{ $signupErrors->has('last_name') ? ' is-invalid' : '' }}">
                            @if($signupErrors->has('last_name'))
                            <div class="invalid-feedback">{{ $signupErrors->first('last_name') }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <select name="role" required class="form-select{{ $signupErrors->has('role') ? ' is-invalid' : '' }}">
                                <option value="Owner" @selected(old('role', 'Owner') === 'Owner')>Owner</option>
                                <option value="Contractor" @selected(old('role') === 'Contractor')>Contractor</option>
                            </select>
                            @if($signupErrors->has('role'))
                            <div class="invalid-feedback">{{ $signupErrors->first('role') }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Your Business Email" required class="form-control{{ $signupErrors->has('email') ? ' is-invalid' : '' }}">
                            @if($signupErrors->has('email'))
                            <div class="invalid-feedback">{{ $signupErrors->first('email') }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" placeholder="Create strong password" required class="form-control{{ $signupErrors->has('password') ? ' is-invalid' : '' }}">
                            @if($signupErrors->has('password'))
                            <div class="invalid-feedback">{{ $signupErrors->first('password') }}</div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                        <p class="text-muted text-center small mt-3 mb-0">
                            Already have an account?
                            <button type="button" class="btn btn-link p-0 align-baseline" onclick="switchAuthForm('login')">Sign In</button>
                        </p>
                    </form>

                    <form id="loginForm" action="{{ route('login.submit') }}" method="POST" class="d-none">
                        @csrf
                        <h3 class="h4 fw-bold text-primary mb-1">Welcome Back</h3>
                        <p class="text-secondary small mb-3">Sign in to your account</p>
                        @if($loginErrors->any())
                        <div class="alert alert-danger mb-3" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach($loginErrors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="mb-3">
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Your Business Email" required class="form-control{{ $loginErrors->has('email') ? ' is-invalid' : '' }}">
                            @if($loginErrors->has('email'))
                            <div class="invalid-feedback">{{ $loginErrors->first('email') }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" placeholder="Your Password" required class="form-control{{ $loginErrors->has('password') ? ' is-invalid' : '' }}">
                            @if($loginErrors->has('password'))
                            <div class="invalid-feedback">{{ $loginErrors->first('password') }}</div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                        <p class="text-muted text-center small mt-3 mb-0">
                            Do not have an account?
                            <button type="button" class="btn btn-link p-0 align-baseline" onclick="switchAuthForm('signup')">Sign Up</button>
                        </p>
                    </form>
                    <div class="auth-branding">
                        <div class="auth-logo">N</div>
                        <p class="small text-muted mb-0">Stay connected with Nirmaan</p>
                    </div>
                    <div class="auth-socials">
                        @foreach (config('branding.social_links', []) as $platform => $url)
                        <a href="{{ $url }}" target="_blank" rel="noreferrer" aria-label="{{ ucfirst($platform) }}">
                            <i class="bi bi-{{ $platform === 'twitter' ? 'twitter' : $platform }}"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function showAuthForm(formType) {
            const modalEl = document.getElementById('authModal');
            const signupForm = document.getElementById('signupForm');
            const loginForm = document.getElementById('loginForm');

            signupForm.classList.toggle('d-none', formType !== 'signup');
            loginForm.classList.toggle('d-none', formType !== 'login');

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        function switchAuthForm(formType) {
            showAuthForm(formType);
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (window.__SHOW_AUTH_MODAL__) {
                showAuthForm(window.__SHOW_AUTH_MODAL__);
            }

            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                setTimeout(function () {
                    flashMessage.classList.add('opacity-0');
                    flashMessage.style.transition = 'opacity 0.6s ease';
                    setTimeout(function () {
                        flashMessage.remove();
                    }, 700);
                }, 3000);
            }
        });
    </script>
</body>
</html>
