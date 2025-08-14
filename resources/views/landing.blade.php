<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Royal Exchange - Premium VTU & Bill Payment Services</title>
    <meta name="description" content="Experience premium VTU services and seamless bill payments with Royal Exchange - Your trusted digital payment partner.">
    <meta name="keywords" content="vtu, bill payment, airtime, data, electricity, tv subscription, royal exchange">

    <!-- Favicons -->
    <link href="assets_land/img/favicon.png" rel="icon">
    <link href="assets_land/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: linear-gradient(135deg, #d4af37 0%, #ffd700 50%, #ffb347 100%);
            --primary-solid: #d4af37;
            --secondary: #ffa500;
            --accent: #ffb347;
            --dark: #0f0f15;
            --dark-light: #1a1a1a;
            --text-light: #f5f5dc;
            --text-muted: #d2b48c;
            --surface: #2c2416;
            --surface-light: rgba(212, 175, 55, 0.1);
            --gradient-bg: linear-gradient(135deg, #d4af37 0%, #ffd700 50%, #ffb347 100%);
            --glass: rgba(212, 175, 55, 0.15);
            --border: rgba(212, 175, 55, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: var(--text-light);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Background Animation */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: var(--gradient-bg);
            opacity: 0.1;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1rem 0;
            background: rgba(15, 15, 35, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .header.scrolled {
            background: rgba(15, 15, 35, 0.95);
            padding: 0.5rem 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }

        .logo {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo::before {
            content: '👑';
            font-size: 1.2rem;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-menu a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-menu a:hover::after,
        .nav-menu a.active::after {
            width: 100%;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--glass);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            background: var(--primary-solid);
            transform: translateY(-2px);
        }

        /* Mobile menu toggle */
        .mobile-toggle {
            display: none;
            flex-direction: column;
            gap: 4px;
            cursor: pointer;
            padding: 8px;
            position: relative;
            z-index: 1001;
        }

        .mobile-toggle span {
            width: 25px;
            height: 3px;
            background: var(--text-light);
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .mobile-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .mobile-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 8rem 2rem 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            z-index: 2;
        }

        .hero-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #f5f5dc 50%, #d2b48c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.3rem);
            color: var(--text-muted);
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            background: var(--primary);
            border: none;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(212, 175, 55, 0.4);
            color: white;
        }

        .floating-cards {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .floating-card {
            position: absolute;
            width: 60px;
            height: 60px;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            animation: floatCard 6s ease-in-out infinite;
        }

        .floating-card:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .floating-card:nth-child(2) { top: 60%; left: 80%; animation-delay: 2s; }
        .floating-card:nth-child(3) { top: 30%; left: 85%; animation-delay: 4s; }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }

        /* Services Section */
        .services {
            padding: 8rem 2rem;
            position: relative;
        }

        .section-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            margin-bottom: 1rem;
            background: var(--primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .service-card {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem;
            backdrop-filter: blur(20px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
        }

        .service-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-light);
        }

        .service-description {
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* App Download Section */
        .app-section {
            padding: 8rem 2rem;
            background: linear-gradient(135deg, var(--surface) 0%, var(--dark-light) 100%);
            position: relative;
            overflow: hidden;
        }

        .app-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpolygon points='50 0 60 40 100 50 60 60 50 100 40 60 0 50 40 40'/%3E%3C/g%3E%3C/svg%3E");
        }

        .app-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .download-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .download-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 15px;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
        }

        .download-btn:hover {
            background: var(--primary-solid);
            transform: translateY(-3px);
            color: white;
        }

        /* Contact Section */
        .contact {
            padding: 8rem 2rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 4rem;
            margin-top: 4rem;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 15px;
            backdrop-filter: blur(20px);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .contact-form {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem;
            backdrop-filter: blur(20px);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-solid);
            background: rgba(255, 255, 255, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .submit-btn {
            width: 100%;
            padding: 1rem 2rem;
            background: var(--primary);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
        }

        /* Footer */
        .footer {
            background: var(--dark-light);
            border-top: 1px solid var(--border);
            padding: 3rem 2rem 2rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h4 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-light);
        }

        .footer-section p,
        .footer-section a {
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 0.5rem;
            display: block;
        }

        .footer-section a:hover {
            color: var(--primary-solid);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background: rgba(15, 15, 21, 0.95);
                backdrop-filter: blur(20px);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 3rem;
                z-index: 1000;
                font-size: 1.2rem;
            }

            .nav-menu.active {
                display: flex;
            }

            .mobile-toggle {
                display: flex;
            }

            .contact-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .nav-container {
                padding: 0 1rem;
            }

            .hero,
            .services,
            .app-section,
            .contact {
                padding: 4rem 1rem;
            }
        }

        /* Scroll animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Scroll indicator */
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: var(--primary);
            z-index: 9999;
            transition: width 0.1s ease;
        }
    </style>
</head>

<body>
    <div class="animated-bg"></div>
    <div class="scroll-indicator"></div>

    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <a href="#" class="logo">Royal Exchange</a>
            
            <nav>
                <ul class="nav-menu">
                    <li><a href="#hero" class="active">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#download">App</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>

            <div class="social-links">
                <a href="#">𝕏</a>
                <a href="#">📘</a>
                <a href="https://instagram.com/royalexchange001">📷</a>
                <a href="#">💼</a>
            </div>

            <div class="mobile-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section id="hero" class="hero">
            <div class="floating-cards">
                <div class="floating-card"></div>
                <div class="floating-card"></div>
                <div class="floating-card"></div>
            </div>
            
            <div class="hero-container">
                <h1 class="hero-title fade-in-up">Premium Digital Services at Royal Exchange</h1>
                <p class="hero-subtitle fade-in-up">Experience seamless VTU services, instant bill payments, and premium TV subscriptions with Nigeria's most trusted digital payment platform.</p>
                <a href="#services" class="cta-button fade-in-up">
                    <span>Explore Services</span>
                    <span>✨</span>
                </a>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="services">
            <div class="section-container">
                <div class="section-header fade-in-up">
                    <h2 class="section-title">Our Premium Services</h2>
                    <p class="section-subtitle">Discover our comprehensive range of digital services designed to meet all your payment and subscription needs.</p>
                </div>

                <div class="services-grid">
                    <div class="service-card fade-in-up">
                        <div class="service-icon">📱</div>
                        <h3 class="service-title">VTU Services & Bill Payment</h3>
                        <p class="service-description">Top up airtime, purchase data bundles, and pay electricity bills instantly with our secure and reliable platform.</p>
                    </div>

                    <div class="service-card fade-in-up">
                        <div class="service-icon">📺</div>
                        <h3 class="service-title">TV Subscriptions</h3>
                        <p class="service-description">Pay for your DSTV, GOTV, and other TV subscriptions effortlessly with instant activation and competitive rates.</p>
                    </div>

                    <div class="service-card fade-in-up">
                        <div class="service-icon">⚡</div>
                        <h3 class="service-title">Instant Transactions</h3>
                        <p class="service-description">Lightning-fast processing with 99.9% uptime guarantee. Your transactions are processed instantly 24/7.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- App Download Section -->
        <section id="download" class="app-section">
            <div class="section-container">
                <div class="app-content fade-in-up">
                    <h2 class="section-title">Download Royal Exchange App</h2>
                    <p class="section-subtitle">Get the Royal Exchange mobile app for seamless transactions on the go. Available on both Android and iOS platforms.</p>
                    
                    <div class="download-buttons">
                        <a href="https://play.google.com/store/apps/details?id=com.royalexchange" class="download-btn" target="_blank">
                            <span>📱</span>
                            <span>Google Play</span>
                        </a>
                        <a href="https://www.apple.com/app-store/" class="download-btn" target="_blank">
                            <span>🍎</span>
                            <span>App Store</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact">
            <div class="section-container">
                <div class="section-header fade-in-up">
                    <h2 class="section-title">Get In Touch</h2>
                    <p class="section-subtitle">Have questions or need support? We're here to help you 24/7.</p>
                </div>

                <div class="contact-grid">
                    <div class="contact-info fade-in-up">
                        <div class="contact-item">
                            <div class="contact-icon">📍</div>
                            <div>
                                <h4>Visit Us</h4>
                                <p>Ore ofe compound adehun<br>Abeokuta, Ogun State, Nigeria</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">📞</div>
                            <div>
                                <h4>Call Us</h4>
                                <p>+234 703 834 2861</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">✉️</div>
                            <div>
                                <h4>Email Us</h4>
                                <p>support@royal-exchange.com.ng</p>
                            </div>
                        </div>
                    </div>

                    <form class="contact-form fade-in-up">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Subject" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="6" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Send Message</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Royal Exchange</h4>
                <p>Your trusted partner for premium digital payment services in Nigeria.</p>
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <a href="#" style="display: inline;">𝕏</a>
                    <a href="#" style="display: inline;">📘</a>
                    <a href="https://instagram.com/royalexchange001" style="display: inline;">📷</a>
                    <a href="#" style="display: inline;">💼</a>
                </div>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="#hero">Home</a>
                <a href="#services">Services</a>
                <a href="#download">Mobile App</a>
                <a href="#contact">Contact</a>
            </div>

            <div class="footer-section">
                <h4>Services</h4>
                <a href="#">VTU Services</a>
                <a href="#">Bill Payments</a>
                <a href="#">TV Subscriptions</a>
                <a href="#">Data Bundles</a>
            </div>

            <div class="footer-section">
                <h4>Contact Info</h4>
                <p>📍 Ore ofe compound adehun, Abeokuta</p>
                <p>📞 +234 703 834 2861</p>
                <p>✉️ support@royal-exchange.com.ng</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Royal Exchange. All rights reserved. | Designed by mgeniusoftware</p>
        </div>
    </footer>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            const scrollIndicator = document.querySelector('.scroll-indicator');
            
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            // Update scroll indicator
            const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            scrollIndicator.style.width = scrollPercent + '%';
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Active nav link highlighting
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-menu a');
            
            let currentSection = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                if (window.scrollY >= sectionTop) {
                    currentSection = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${currentSection}`) {
                    link.classList.add('active');
                }
            });
        });

        // Form submission
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simple form validation and submission feedback
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            // Simulate form submission
            setTimeout(() => {
                submitBtn.textContent = 'Message Sent! ✓';
                submitBtn.style.background = '#10b981';
                
                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    submitBtn.style.background = '';
                    this.reset();
                }, 2000);
            }, 1500);
        });

        // Mobile menu toggle
        const mobileToggle = document.querySelector('.mobile-toggle');
        const navMenu = document.querySelector('.nav-menu');
        
        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            mobileToggle.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                mobileToggle.classList.remove('active');
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                mobileToggle.classList.remove('active');
            }
        });

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all fade-in elements
        document.querySelectorAll('.fade-in-up').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            observer.observe(el);
        });

        // Service cards hover effect enhancement
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Parallax effect for floating cards
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.floating-card');
            
            parallaxElements.forEach((element, index) => {
                const speed = 0.5 + (index * 0.1);
                element.style.transform = `translateY(${scrolled * speed}px) rotate(${scrolled * 0.05}deg)`;
            });
        });

        // Add loading animation
        window.addEventListener('load', () => {
            document.body.style.opacity = '1';
            document.body.style.transition = 'opacity 0.5s ease';
        });

        // Initialize
        document.body.style.opacity = '0';
    </script>
</body>
</html>