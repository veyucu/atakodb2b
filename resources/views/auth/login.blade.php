<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteSettings = \App\Models\SiteSetting::getSettings();
    @endphp
    <title>Giriş - {{ $siteSettings->site_name ?? config('app.name', 'atakodb2b') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            overflow: hidden;
            position: relative;
        }

        #particles-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .floating-shape {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            pointer-events: none;
            z-index: 2;
            transition: transform 0.5s ease-out, filter 0.5s ease-out;
            animation: float 15s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-30px, 30px) rotate(240deg);
            }
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
            animation-duration: 20s;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            bottom: 10%;
            right: 10%;
            animation-delay: -7s;
            animation-duration: 25s;
        }

        .shape-3 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            top: 50%;
            left: 50%;
            animation-delay: -14s;
            animation-duration: 18s;
        }

        .login-container {
            position: relative;
            z-index: 50;
            width: 100%;
            max-width: 380px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
        }

        .login-header {
            padding: 30px 25px 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.6);
            position: relative;
        }

        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            background: white;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05) rotate(5deg);
        }

        .logo img { 
            max-width: 50px; 
            max-height: 50px;
        }
        
        .logo i {
            font-size: 2.2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .site-name {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
        }

        .subtitle { 
            font-size: 0.9rem; 
            color: #64748b;
            font-weight: 500;
            margin: 0;
        }

        .login-body { 
            padding: 28px; 
            background: white; 
        }

        .form-group { 
            margin-bottom: 18px; 
        }

        .form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            display: block;
        }

        .input-group { 
            position: relative; 
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 13px 14px 13px 42px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control:focus + .input-icon {
            color: #667eea;
        }

        .form-check {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 5px;
            cursor: pointer;
            margin: 0;
            transition: all 0.3s;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            font-size: 0.88rem;
            color: #475569;
            margin-left: 8px;
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login span {
            position: relative;
            z-index: 1;
        }

        .alert {
            padding: 12px 16px;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-radius: 10px;
            color: #991b1b;
            font-size: 0.88rem;
            margin-bottom: 18px;
            border-left: 4px solid #dc2626;
            word-break: break-word;
        }

        .company-info-card {
            margin-top: 15px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .company-info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
        }

        .company-info-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.85rem;
            color: #334155;
            word-break: break-word;
        }

        .info-row.full-width {
            width: 100%;
        }

        .info-row span {
            flex: 1;
            line-height: 1.5;
        }

        .contact-row {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #334155;
            word-break: break-word;
        }

        .contact-item span {
            line-height: 1.5;
        }

        .info-row i,
        .contact-item i {
            color: #667eea;
            font-size: 0.95rem;
            width: 18px;
            min-width: 18px;
            text-align: center;
            flex-shrink: 0;
        }


        @media (max-width: 576px) {
            body {
                padding: 8px;
                min-height: 100vh;
                display: flex;
                align-items: center;
            }

            .login-container { 
                max-width: 100%;
                width: 100%;
                padding: 0;
            }
            
            .login-card {
                margin: 0 auto;
                border-radius: 16px;
            }

            .login-header {
                padding: 18px 15px 12px;
            }

            .logo {
                width: 55px;
                height: 55px;
                margin-bottom: 10px;
                border-radius: 14px;
            }

            .logo img {
                max-width: 40px;
                max-height: 40px;
            }

            .logo i {
                font-size: 1.8rem;
            }

            .site-name {
                font-size: 1.3rem;
                margin-bottom: 4px;
            }

            .subtitle {
                font-size: 0.78rem;
                margin: 0;
            }

            .login-body {
                padding: 16px 15px;
            }

            .form-group {
                margin-bottom: 14px;
            }

            .form-label {
                font-size: 0.8rem;
                margin-bottom: 5px;
            }

            .form-control {
                padding: 11px 10px 11px 36px;
                font-size: 14px;
                border-radius: 8px;
            }

            .input-icon {
                left: 11px;
                font-size: 0.88rem;
            }

            .form-check {
                margin-bottom: 16px;
            }

            .form-check-input {
                width: 16px;
                height: 16px;
            }

            .form-check-label {
                font-size: 0.8rem;
                margin-left: 7px;
            }

            .btn-login {
                padding: 12px;
                font-size: 0.92rem;
                border-radius: 8px;
            }

            .alert {
                padding: 9px 12px;
                font-size: 0.78rem;
                margin-bottom: 14px;
                border-radius: 8px;
            }

            .company-info-card {
                padding: 10px 12px;
                margin-top: 8px;
                border-radius: 12px;
                width: 100%;
            }

            .company-info-content {
                gap: 7px;
            }

            .info-row {
                font-size: 0.72rem;
                gap: 7px;
                align-items: flex-start;
            }

            .info-row i,
            .contact-item i {
                font-size: 0.82rem;
                width: 15px;
                min-width: 15px;
            }

            .contact-item {
                font-size: 0.72rem;
                gap: 6px;
            }

            .contact-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 7px;
            }

            .floating-shape {
                filter: blur(40px);
                opacity: 0.3;
            }

            .shape-1 {
                width: 200px;
                height: 200px;
            }

            .shape-2 {
                width: 250px;
                height: 250px;
            }

            .shape-3 {
                width: 180px;
                height: 180px;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 5px;
            }

            .login-header {
                padding: 15px 12px 10px;
            }

            .logo {
                width: 50px;
                height: 50px;
                margin-bottom: 8px;
            }

            .site-name {
                font-size: 1.2rem;
            }

            .subtitle {
                font-size: 0.75rem;
            }

            .login-body {
                padding: 14px 12px;
            }

            .company-info-card {
                padding: 9px 10px;
                margin-top: 7px;
            }

            .info-row,
            .contact-item {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    <canvas id="particles-canvas"></canvas>
    
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    @if($siteSettings->logo_url)
                        <img src="{{ $siteSettings->logo_url }}" alt="{{ $siteSettings->site_name }}">
                    @else
                        <i class="fas fa-store"></i>
                    @endif
                </div>
                <h1 class="site-name">{{ $siteSettings->site_name ?? 'atakodb2b' }}</h1>
                <p class="subtitle">Hesabınıza Giriş Yapın</p>
            </div>

            <div class="login-body">
                @if ($errors->any())
                    <div class="alert">
                        @foreach ($errors->all() as $error)
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $error }}
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Kullanıcı Adı</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="username" 
                                   value="{{ old('username') }}" required autofocus>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Şifre</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Beni Hatırla</label>
                    </div>

                    <button type="submit" class="btn-login">
                        <span>
                            <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                        </span>
                    </button>
                </form>
            </div>
        </div>

        @if($siteSettings->company_address || $siteSettings->company_phone || $siteSettings->company_email)
        <div class="company-info-card">
            <div class="company-info-content">
                @if($siteSettings->company_address)
                    <div class="info-row full-width">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $siteSettings->company_address }}</span>
                    </div>
                @endif
                @if($siteSettings->company_phone || $siteSettings->company_email)
                    <div class="contact-row">
                        @if($siteSettings->company_phone)
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span>{{ $siteSettings->company_phone }}</span>
                            </div>
                        @endif
                        @if($siteSettings->company_email)
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $siteSettings->company_email }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Particle System with Auto Movement and Mouse Interaction
        const canvas = document.getElementById('particles-canvas');
        const ctx = canvas.getContext('2d');
        
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        let mouseX = -1000;
        let mouseY = -1000;
        let particles = [];
        let time = 0;
        let mouseActive = false;

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 3 + 1;
                this.baseSpeedX = Math.random() * 0.5 - 0.25;
                this.baseSpeedY = Math.random() * 0.5 - 0.25;
                this.speedX = this.baseSpeedX;
                this.speedY = this.baseSpeedY;
                this.angle = Math.random() * Math.PI * 2;
                this.hue = Math.random() * 60 + 200; // Blue-Purple range
                this.pulseSpeed = Math.random() * 0.02 + 0.01;
                this.pulseOffset = Math.random() * Math.PI * 2;
            }

            update(time) {
                // Auto wave/flow movement
                const wave = Math.sin(time * 0.001 + this.pulseOffset) * 0.5;
                const autoFlowX = Math.sin(time * 0.0005 + this.angle) * 0.3;
                const autoFlowY = Math.cos(time * 0.0005 + this.angle) * 0.3;

                // Mouse attraction (stronger when mouse is active)
                const dx = mouseX - this.x;
                const dy = mouseY - this.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                const maxDistance = mouseActive ? 200 : 150;

                if (distance < maxDistance && mouseActive) {
                    const force = (maxDistance - distance) / maxDistance;
                    const angle = Math.atan2(dy, dx);
                    this.speedX += Math.cos(angle) * force * 0.4; // Stronger attraction
                    this.speedY += Math.sin(angle) * force * 0.4;
                    
                    // Color change on mouse proximity
                    this.hue = 280 + (force * 40); // Shift to purple/pink
                } else {
                    // Return to base movement
                    this.speedX += (this.baseSpeedX - this.speedX) * 0.02;
                    this.speedY += (this.baseSpeedY - this.speedY) * 0.02;
                    this.hue += (220 - this.hue) * 0.05; // Return to blue
                }

                // Apply auto flow and velocity
                this.x += this.speedX + autoFlowX + wave * 0.2;
                this.y += this.speedY + autoFlowY;

                // Gentle friction
                this.speedX *= 0.97;
                this.speedY *= 0.97;

                // Wrap around screen
                if (this.x > canvas.width) this.x = 0;
                if (this.x < 0) this.x = canvas.width;
                if (this.y > canvas.height) this.y = 0;
                if (this.y < 0) this.y = canvas.height;

                // Pulse size
                this.currentSize = this.size + Math.sin(time * this.pulseSpeed) * 0.5;
            }

            draw() {
                const alpha = mouseActive && 
                    Math.sqrt((mouseX - this.x)**2 + (mouseY - this.y)**2) < 200 
                    ? 0.8 : 0.6;
                
                ctx.fillStyle = `hsla(${this.hue}, 70%, 70%, ${alpha})`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.currentSize, 0, Math.PI * 2);
                ctx.fill();

                // Enhanced glow effect
                const glowSize = mouseActive ? 15 : 10;
                ctx.shadowBlur = glowSize;
                ctx.shadowColor = `hsla(${this.hue}, 70%, 70%, ${alpha})`;
                ctx.fill();
                ctx.shadowBlur = 0;
            }
        }

        // Create particles
        for (let i = 0; i < 80; i++) {
            particles.push(new Particle());
        }

        function connectParticles() {
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    const maxConnectDistance = mouseActive ? 150 : 120;
                    if (distance < maxConnectDistance) {
                        const opacity = mouseActive ? 0.3 : 0.2;
                        ctx.strokeStyle = `rgba(255, 255, 255, ${opacity - distance / 600})`;
                        ctx.lineWidth = mouseActive ? 1.5 : 1;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
        }

        function drawMouseGlow() {
            if (mouseActive && mouseX > 0 && mouseY > 0) {
                const gradient = ctx.createRadialGradient(mouseX, mouseY, 0, mouseX, mouseY, 150);
                gradient.addColorStop(0, 'rgba(255, 255, 255, 0.1)');
                gradient.addColorStop(0.5, 'rgba(200, 150, 255, 0.05)');
                gradient.addColorStop(1, 'transparent');
                
                ctx.fillStyle = gradient;
                ctx.beginPath();
                ctx.arc(mouseX, mouseY, 150, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function animate() {
            time++;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            drawMouseGlow();

            particles.forEach(particle => {
                particle.update(time);
                particle.draw();
            });

            connectParticles();
            requestAnimationFrame(animate);
        }

        // Mouse tracking
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            mouseActive = true;

            // Move floating shapes with parallax (enhanced)
            const shapes = document.querySelectorAll('.floating-shape');
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 0.05; // More responsive
                const x = (e.clientX - window.innerWidth / 2) * speed;
                const y = (e.clientY - window.innerHeight / 2) * speed;
                const scale = 1 + speed * 0.3;
                shape.style.transform = `translate(${x}px, ${y}px) scale(${scale})`;
                shape.style.filter = `blur(${60 + speed * 100}px)`; // Dynamic blur
                shape.style.opacity = 0.5 + speed * 0.5; // Brighter on hover
            });
        });

        // Mouse leave - reset
        document.addEventListener('mouseleave', () => {
            mouseActive = false;
            mouseX = -1000;
            mouseY = -1000;
        });

        // Auto-animate floating shapes when no mouse
        let autoTime = 0;
        function autoAnimateShapes() {
            if (!mouseActive) {
                autoTime += 0.01;
                const shapes = document.querySelectorAll('.floating-shape');
                shapes.forEach((shape, index) => {
                    const offsetX = Math.sin(autoTime + index) * 30;
                    const offsetY = Math.cos(autoTime + index * 0.7) * 30;
                    shape.style.transform = `translate(${offsetX}px, ${offsetY}px)`;
                    shape.style.filter = 'blur(60px)'; // Reset blur
                    shape.style.opacity = '0.4'; // Reset opacity
                });
            }
            requestAnimationFrame(autoAnimateShapes);
        }

        // Resize handler
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // Start animations
        animate();
        autoAnimateShapes();
    </script>
</body>
</html>
