<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reham Futsal</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3B82F6;
            --navy-blue: #1E3A8A;
            --cyan-bright: #06B6D4;
            --teal-accent: #14B8A6;
            --orange-warm: #F59E0B;
            --slate-dark: #1F2937;
            --slate-darker: #111827;
            --charcoal: #374151;
            --gray-light: #9CA3AF;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
        }

        * {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #f8f9fb 0%, #e5e7eb 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(30, 58, 138, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            animation: fadeInUp 0.6s ease-out;
        }

        .login-header {
            background: linear-gradient(135deg, var(--navy-blue) 0%, #1e40af 100%);
            padding: 2rem 1.5rem;
            text-align: center;
            color: white;
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--cyan-bright) 0%, var(--teal-accent) 100%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.4);
            animation: float 3s ease-in-out infinite;
        }

        .login-body {
            padding: 2rem 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--cyan-bright);
            background: white;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--cyan-bright) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            margin-top: 1rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
            color: #6b7280;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
        }

        .btn-google {
            width: 100%;
            padding: 0.875rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #374151;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-google:hover {
            border-color: #dc2626;
            background: #fef2f2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding: 1rem;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .login-footer a {
            color: var(--cyan-bright);
            font-weight: 600;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .back-home {
            position: absolute;
            top: 1rem;
            left: 1rem;
            color: white;
            font-size: 1.5rem;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .back-home:hover {
            opacity: 1;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        #toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 100;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transform: translateX(120%);
            transition: transform 0.4s ease;
            max-width: 320px;
        }

        #toast.show {
            transform: translateX(0);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header relative">
            <!-- Kembali ke Home -->
            <a href="../index.php" class="back-home">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="logo-icon">
                <i class="fas fa-futbol text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold">Selamat Datang Kembali!</h1>
            <p class="text-sm mt-1 opacity-90">Masuk untuk mulai booking lapangan futsal</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <form id="loginForm">
                <!-- Email -->
                <div class="form-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input 
                        type="email" 
                        class="form-control" 
                        placeholder="Email" 
                        required
                        value="user@example.com"
                    >
                </div>

                <!-- Password -->
                <div class="form-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        class="form-control" 
                        placeholder="Password" 
                        required
                        value="password123"
                    >
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" class="mr-2" checked>
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm text-cyan-600 hover:underline">
                        Lupa password?
                    </a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <span>atau</span>
            </div>

            <!-- Google Login -->
            <button class="btn-google">
                <img src="https://www.google.com/favicon.ico" alt="Google" class="w-5 h-5">
                <span>Masuk dengan Google</span>
            </button>

            <!-- Register Link -->
            <div class="login-footer">
                <p>Belum punya akun? 
                    <a href="daftar.php">Daftar Sekarang</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="hidden">
        <i id="toast-icon" class="text-2xl"></i>
        <p id="toast-message" class="font-medium"></p>
        <button onclick="hideToast()" class="ml-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script>
        // Form submit handler (demo)
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.querySelector('input[type="email"]').value;
            const password = this.querySelector('input[type="password"]').value;

            if (email === 'user@example.com' && password === 'password123') {
                showToast('Login berhasil! Mengarahkan ke dashboard...', 'success');
                setTimeout(() => {
                    window.location.href = 'dashboard.php'; // Path relatif di folder user/
                }, 2000);
            } else {
                showToast('Email atau password salah!', 'error');
            }
        });

        // Toast function
        let toastTimeout;
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toast-icon');
            const toastMessage = document.getElementById('toast-message');

            if (toastTimeout) clearTimeout(toastTimeout);

            const styles = {
                success: { 
                    icon: 'fas fa-check-circle text-green-500',
                    border: 'border-l-4 border-green-500'
                },
                error: { 
                    icon: 'fas fa-exclamation-circle text-red-500',
                    border: 'border-l-4 border-red-500'
                },
                info: { 
                    icon: 'fas fa-info-circle text-blue-500',
                    border: 'border-l-4 border-blue-500'
                }
            };

            toast.className = `fixed top-4 right-4 z-50 bg-white rounded-lg shadow-2xl p-4 max-w-sm border-l-4 ${styles[type].border}`;
            toastIcon.className = styles[type].icon;
            toastMessage.textContent = message;

            toast.classList.remove('hidden');
            toast.style.animation = 'slideIn 0.4s ease';

            toastTimeout = setTimeout(hideToast, 4000);
        }

        function hideToast() {
            const toast = document.getElementById('toast');
            toast.style.animation = 'slideOut 0.4s ease';
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 400);
        }

        // Animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Auto-fill info
        window.addEventListener('load', () => {
            setTimeout(() => {
                showToast('Gunakan: user@example.com / password123', 'info');
            }, 1000);
        });
    </script>
</body>
</html>