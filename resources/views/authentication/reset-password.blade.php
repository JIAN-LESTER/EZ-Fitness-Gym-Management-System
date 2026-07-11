<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EZ Fitness</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo_image/ez_fitness_gym_logo.png') }}">
  
  <!-- Load SweetAlert2 CSS -->
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
      width: 200px;
      height: 200px;
      background: linear-gradient(45deg, #ff6b6b, #feca57);
      top: 10%;
      left: -50px;
      transform: rotate(45deg);
    }

    .shape-2 {
      width: 150px;
      height: 150px;
      background: linear-gradient(45deg, #feca57, #ff6b6b);
      bottom: 20%;
      left: 10%;
      transform: rotate(-30deg);
    }

    .shape-3 {
      width: 100px;
      height: 100px;
      background: linear-gradient(45deg, #ff9ff3, #feca57);
      top: 50%;
      left: 30%;
      transform: rotate(15deg);
    }

    @keyframes shake {
      0%,
      100% {
        transform: translateX(0);
      }

      10%,
      30%,
      50%,
      70%,
      90% {
        transform: translateX(-5px);
      }

      20%,
      40%,
      60%,
      80% {
        transform: translateX(5px);
      }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(5deg); }
    }

    .decorative-shape {
      animation: float 6s ease-in-out infinite;
    }

    .shape-2 {
      animation-delay: 1s;
      animation-duration: 8s;
    }

    .shape-3 {
      animation-delay: 2s;
      animation-duration: 7s;
    }
  </style>
</head>

<body class="flex items-center bg-gray-300 justify-center min-h-screen p-4">
  <main class="w-full max-w-6xl bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col md:flex-row min-h-[600px]">
    
    <!-- Left Side - Info Section -->
    <section class="hidden md:flex md:w-1/2 info-section text-white p-12 flex-col justify-center relative">
      <div class="decorative-shape shape-1"></div>
      <div class="decorative-shape shape-2"></div>
      <div class="decorative-shape shape-3"></div>
      
      <div class="relative z-10">
        <div class="mb-8">
          <svg class="w-24 h-24 mx-auto text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
          </svg>
        </div>
        <h2 class="text-3xl font-bold mb-6 text-center">
          Create New Password
        </h2>
        <p class="text-lg leading-relaxed mb-6 text-center">
          Your new password must be different from previously used passwords for security purposes.
        </p>
        <div class="bg-white bg-opacity-10 rounded-lg p-6 backdrop-blur-sm">
          <h3 class="font-semibold mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Password Requirements
          </h3>
          <ul class="space-y-2 text-sm">
            <li class="flex items-start">
              <svg class="w-4 h-4 mr-2 mt-0.5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              <span>Minimum 6 characters long</span>
            </li>
            <li class="flex items-start">
              <svg class="w-4 h-4 mr-2 mt-0.5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              <span>Must match confirmation</span>
            </li>
            <li class="flex items-start">
              <svg class="w-4 h-4 mr-2 mt-0.5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              <span>Different from old password</span>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Right Side - Reset Password Form -->
    <section class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
      <header class="mb-8">
        <div class="flex items-center justify-center md:justify-start mb-4">
          <div class="w-16 h-16">
            <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" alt="EZ Fitness Logo" class="w-full h-full object-contain" />
          </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 text-center md:text-left">
          RESET PASSWORD
        </h1>
        <p class="text-gray-600 mt-2 text-center md:text-left">Enter your new password below</p>
      </header>

      <form id="resetPasswordForm" method="POST" action="/reset-password" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email Address
          </label>
          <input novalidate id="email" name="email" value="{{ $email ?? old('email') }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none bg-gray-50" 
            required readonly />
          @error('email')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <div class="relative">
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            New Password
          </label>
          <input type="password" id="password" name="password" placeholder="Enter new password"
            class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" 
            required />
          <button type="button" id="togglePassword" class="absolute right-3 top-[42px] text-gray-500 hover:text-gray-700">
            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
          @error('password')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
          @enderror
          <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters</p>
        </div>

        <div class="relative">
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
            Confirm New Password
          </label>
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password"
            class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" 
            required />
          <button type="button" id="togglePasswordConfirm" class="absolute right-3 top-[42px] text-gray-500 hover:text-gray-700">
            <svg id="eyeIconConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>

        <button type="submit" id="resetPasswordBtn"
          class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-all shadow-lg hover:shadow-xl">
          RESET PASSWORD
        </button>

        <div class="text-center">
          <a href="{{ route('loginForm') }}" class="inline-flex items-center text-sm text-gray-700 hover:text-gray-900 hover:underline font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Login
          </a>
        </div>
      </form>
    </section>

  </main>

  <!-- Scripts - Load in correct order -->
  <!-- 1. SweetAlert2 Library -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- 2. Notifications Module -->
  <script src="{{ asset('js/notifications.js') }}"></script>

  <!-- 3. Your Custom Scripts -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Show session messages as toasts
      @if(session('success'))
        Notifications.toast('success', '{{ session('success') }}');
      @endif

      @if(session('error'))
        Notifications.toast('error', '{{ session('error') }}');
      @endif

      @if(session('warning'))
        Notifications.toast('warning', '{{ session('warning') }}');
      @endif

      @if(session('info'))
        Notifications.toast('info', '{{ session('info') }}');
      @endif

      @if(session('status'))
        Notifications.toast('success', '{{ session('status') }}');
      @endif
    });

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const eyeIconConfirm = document.getElementById('eyeIconConfirm');

    if (togglePassword && passwordInput && eyeIcon) {
      togglePassword.addEventListener('click', function() {
        togglePasswordVisibility(passwordInput, eyeIcon);
      });
    }

    if (togglePasswordConfirm && passwordConfirmInput && eyeIconConfirm) {
      togglePasswordConfirm.addEventListener('click', function() {
        togglePasswordVisibility(passwordConfirmInput, eyeIconConfirm);
      });
    }

    function togglePasswordVisibility(input, icon) {
      const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
      input.setAttribute('type', type);

      if (type === 'text') {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
      } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
      }
    }

    // Form validation
    const form = document.getElementById('resetPasswordForm');
    const resetPasswordBtn = document.getElementById('resetPasswordBtn');

    if (form && resetPasswordBtn) {
      form.addEventListener('submit', function(e) {
        let isValid = true;

        // Password validation
        if (!passwordInput.value.trim()) {
          showError(passwordInput, 'Password is required');
          isValid = false;
        } else if (passwordInput.value.length < 6) {
          showError(passwordInput, 'Password must be at least 6 characters');
          isValid = false;
        } else {
          clearError(passwordInput);
        }

        // Password confirmation validation
        if (!passwordConfirmInput.value.trim()) {
          showError(passwordConfirmInput, 'Please confirm your password');
          isValid = false;
        } else if (passwordInput.value !== passwordConfirmInput.value) {
          showError(passwordConfirmInput, 'Passwords do not match');
          isValid = false;
        } else {
          clearError(passwordConfirmInput);
        }

        if (!isValid) {
          e.preventDefault();
          return;
        }

        // Show loading state
        resetPasswordBtn.disabled = true;
        resetPasswordBtn.innerHTML = '<span class="animate-pulse">Resetting password...</span>';
        Notifications.loading('Resetting your password...');
      });
    }

    function showError(input, message) {
      input.classList.add('border-red-500');
      input.classList.remove('border-gray-300');

      input.style.animation = 'shake 0.5s';
      setTimeout(() => {
        input.style.animation = '';
      }, 500);

      let errorSpan = input.parentNode.querySelector('.error-message');
      if (!errorSpan) {
        errorSpan = document.createElement('span');
        errorSpan.className = 'error-message text-red-500 text-xs mt-1 block';
        input.parentNode.appendChild(errorSpan);
      }
      errorSpan.textContent = message;
    }

    function clearError(input) {
      input.classList.remove('border-red-500');
      input.classList.add('border-gray-300');

      const errorSpan = input.parentNode.querySelector('.error-message');
      if (errorSpan) {
        errorSpan.remove();
      }
    }
  </script>
</body>
</html> 
