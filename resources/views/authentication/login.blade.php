<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign in | EZ Fitness</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo_image/ez_fitness_gym_logo.png') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <style>
    .info-section {
      background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
      position: relative;
      overflow: hidden;
    }

    .decorative-shape {
      position: absolute;
      border-radius: 50%;
      opacity: 0.3;
    }

    .shape-1 {
      width: 200px; height: 200px;
      background: linear-gradient(45deg, #ff6b6b, #feca57);
      top: 10%; right: -50px;
      transform: rotate(45deg);
    }

    .shape-2 {
      width: 150px; height: 150px;
      background: linear-gradient(45deg, #feca57, #ff6b6b);
      bottom: 20%; right: 10%;
      transform: rotate(-30deg);
    }

    .shape-3 {
      width: 100px; height: 100px;
      background: linear-gradient(45deg, #ff9ff3, #feca57);
      top: 50%; right: 30%;
      transform: rotate(15deg);
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(5deg); }
    }

    .decorative-shape { animation: float 6s ease-in-out infinite; }
    .shape-2 { animation-delay: 1s; animation-duration: 8s; }
    .shape-3 { animation-delay: 2s; animation-duration: 7s; }
  </style>
</head>

<body class="flex items-center bg-gray-300 justify-center min-h-screen p-4">
  <main class="w-full max-w-6xl bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col md:flex-row min-h-[600px]">

    <!-- Left Side - Login Form -->
    <section class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
      <header class="mb-8">
        <div class="flex items-center justify-center md:justify-start mb-4">
          <div class="w-16 h-16">
            <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" alt="EZ Fitness Logo" class="w-full h-full object-contain" />
          </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 text-center md:text-left">LOGIN</h1>
        <p class="text-gray-600 mt-2 text-center md:text-left">Welcome back! Please login to your account.</p>
      </header>

      <form id="loginForm" method="POST" action="/login" class="space-y-5">
        @csrf

        {{-- Route server errors: password errors under password field, everything else to banner --}}
        @php
          $serverError        = session('error');
          $lower              = $serverError ? strtolower($serverError) : '';
          // Password-related errors → under password field
          $passwordFieldError = ($serverError && str_contains($lower, 'password')) ? $serverError : null;
          // Everything else (incorrect credentials, no account, not verified, etc.) → banner
          $bannerError        = ($serverError && !$passwordFieldError) ? $serverError : null;
        @endphp

        @if($bannerError)
          <div class="flex items-start gap-2.5 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ $bannerError }}</span>
          </div>
        @endif

        <div>
          <label for="login" class="block text-sm font-medium text-gray-700 mb-2">Username or Email</label>
          <input type="text" id="login" name="login" placeholder="Enter your username or email"
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none"
            value="{{ old('login') }}" autocomplete="username" />
          @error('login')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <div class="relative">
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password"
            class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none @if($passwordFieldError) border-red-400 @endif"
            autocomplete="current-password" />
          <button type="button" id="togglePassword" class="absolute right-3 top-[42px] text-gray-500 hover:text-gray-700">
            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
          @error('password')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
          @enderror
          @if($passwordFieldError)
            <span class="text-red-500 text-xs mt-1 block" id="passwordFieldError">{{ $passwordFieldError }}</span>
          @endif
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center">
            <input type="checkbox" class="w-4 h-4 text-gray-800 border-gray-300 rounded focus:ring-gray-500">
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
          </label>
          <a href="{{ route('password.request') }}" class="text-sm text-gray-700 hover:text-gray-900 hover:underline">
            Forgot password?
          </a>
        </div>

        <button type="submit" id="loginBtn"
          class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-all shadow-lg hover:shadow-xl">
          LOGIN
        </button>

        <p class="text-center text-sm text-gray-600 mt-4">
          Don't have an account?
          <a href="{{ route('register') }}" class="text-gray-800 hover:underline font-medium">Sign Up</a>
        </p>
      </form>

      @if(session('resend_user_id'))
        <form id="resendVerificationForm" method="POST" action="/email/verification-notification" class="mt-4">
          @csrf
          <input type="hidden" name="user_id" value="{{ session('resend_user_id') }}">
          <button type="submit" id="resendBtn"
            class="w-full px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-400 focus:outline-none">
            Resend Verification Email
          </button>
        </form>
      @endif
    </section>

    <!-- Right Side - Info Section -->
    <section class="hidden md:flex md:w-1/2 info-section text-white p-12 flex-col justify-center relative">
      <div class="decorative-shape shape-1"></div>
      <div class="decorative-shape shape-2"></div>
      <div class="decorative-shape shape-3"></div>

      <div class="relative z-10">
        <h2 class="text-4xl font-bold mb-6">
          <span class="text-red-400">EZ</span> FITNESS GYM
        </h2>
        <h3 class="text-2xl font-semibold mb-4">Welcome to EZ Fitness</h3>
        <p class="text-lg leading-relaxed mb-6">
          Transform your body, transform your life. Join our community of fitness enthusiasts and achieve your goals with state-of-the-art equipment and expert guidance.
        </p>
        <ul class="space-y-3">
          <li class="flex items-center">
            <svg class="w-6 h-6 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Gym Management System
          </li>
          <li class="flex items-center">
            <svg class="w-6 h-6 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            QR-Based Member Check-In
          </li>
          <li class="flex items-center">
            <svg class="w-6 h-6 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Inventory and POS Support for Admins
          </li>
        </ul>
      </div>
    </section>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/notifications.js') }}"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      // ── Close any lingering SweetAlert modal (e.g. loading spinner from previous submit) ──
      if (typeof Swal !== 'undefined') Swal.close();

      // ── Session toasts ──────────────────────────────────────────────────────
      @if(session('success')) Notifications.toast('success', '{{ session('success') }}'); @endif
      {{-- 'error' is shown inline above the form, not as a toast --}}
      @if(session('warning')) Notifications.toast('warning', '{{ session('warning') }}'); @endif
      @if(session('info'))    Notifications.toast('info',    '{{ session('info') }}');    @endif
      @if(session('status'))  Notifications.toast('success', '{{ session('status') }}'); @endif

      // ── Element refs ────────────────────────────────────────────────────────
      const loginForm     = document.getElementById('loginForm');
      const loginBtn      = document.getElementById('loginBtn');
      const loginInput    = document.getElementById('login');
      const passwordInput = document.getElementById('password');

      // ── Inline field error helpers ───────────────────────────────────────────
      function showFieldError(input, message, errorId) {
        input.classList.add('border-red-400');
        input.classList.remove('border-gray-300');
        input.style.animation = 'shake 0.4s';
        setTimeout(() => { input.style.animation = ''; }, 400);
        let el = document.getElementById(errorId);
        if (!el) {
          el = document.createElement('span');
          el.id = errorId;
          el.className = 'text-red-500 text-xs mt-1 block';
          // insert after the input's parent wrapper (after toggle button if present)
          input.closest('div').appendChild(el);
        }
        el.textContent = message;
      }

      function clearFieldError(input, errorId) {
        input.classList.remove('border-red-400');
        input.classList.add('border-gray-300');
        const el = document.getElementById(errorId);
        if (el) el.remove();
      }

      // Clear server-rendered password field error on input
      const serverPwdErr = document.getElementById('passwordFieldError');
      if (serverPwdErr) {
        passwordInput.addEventListener('input', function() {
          clearFieldError(passwordInput, 'passwordFieldError');
        }, { once: true });
      }

      // ── Login form: validate FIRST, then submit ──────────────────────────────
      if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function (e) {
          e.preventDefault();

          let isValid = true;

          if (!loginInput.value.trim()) {
            showFieldError(loginInput, 'Username or email is required.', 'jsLoginError');
            isValid = false;
          } else {
            clearFieldError(loginInput, 'jsLoginError');
          }

          if (!passwordInput.value.trim()) {
            showFieldError(passwordInput, 'Password is required.', 'jsPasswordError');
            isValid = false;
          } else if (passwordInput.value.length < 6) {
            showFieldError(passwordInput, 'Password must be at least 6 characters.', 'jsPasswordError');
            isValid = false;
          } else {
            clearFieldError(passwordInput, 'jsPasswordError');
          }

          if (!isValid) {
            loginBtn.disabled = false;
            loginBtn.innerHTML = 'LOGIN';
            return;
          }

          loginBtn.disabled = true;
          loginBtn.innerHTML = '<span class="animate-pulse">Signing in...</span>';
          loginForm.submit();
        });
      }

      // ── Resend verification loading state ───────────────────────────────────
      @if(session('resend_user_id'))
        const resendForm = document.getElementById('resendVerificationForm');
        const resendBtn  = document.getElementById('resendBtn');

        if (resendForm && resendBtn) {
          resendForm.addEventListener('submit', function () {
            resendBtn.disabled = true;
            resendBtn.innerHTML = '<span class="animate-pulse">Sending...</span>';
          });
        }
      @endif

      // ── Password visibility toggle ──────────────────────────────────────────
      const togglePassword = document.getElementById('togglePassword');
      const eyeIcon        = document.getElementById('eyeIcon');

      const EYE_OPEN  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
      const EYE_CLOSE = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';

      if (togglePassword && passwordInput && eyeIcon) {
        togglePassword.addEventListener('click', function () {
          const show = passwordInput.type === 'password';
          passwordInput.type = show ? 'text' : 'password';
          eyeIcon.innerHTML  = show ? EYE_CLOSE : EYE_OPEN;
        });
      }

    });

  </script>
</body>
</html>
