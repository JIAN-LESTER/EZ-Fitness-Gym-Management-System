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
      right: -50px;
      transform: rotate(45deg);
    }

    .shape-2 {
      width: 150px;
      height: 150px;
      background: linear-gradient(45deg, #feca57, #ff6b6b);
      bottom: 20%;
      right: 10%;
      transform: rotate(-30deg);
    }

    .shape-3 {
      width: 100px;
      height: 100px;
      background: linear-gradient(45deg, #ff9ff3, #feca57);
      top: 50%;
      right: 30%;
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
    
    <!-- Left Side - Forgot Password Form -->
    <section class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
      <header class="mb-8">
        <div class="flex items-center justify-center md:justify-start mb-4">
          <div class="w-16 h-16">
            <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" alt="EZ Fitness Logo" class="w-full h-full object-contain" />
          </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 text-center md:text-left">
          FORGOT PASSWORD?
        </h1>
        <p class="text-gray-600 mt-2 text-center md:text-left">No worries, we'll send you reset instructions.</p>
      </header>

      <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email Address
          </label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="your.email@example.com"
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" 
            required />
          @error('email')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit" id="sendResetBtn"
          class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-all shadow-lg hover:shadow-xl">
          SEND RESET LINK
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

    <!-- Right Side - Info Section -->
    <section class="hidden md:flex md:w-1/2 info-section text-white p-12 flex-col justify-center relative">
      <div class="decorative-shape shape-1"></div>
      <div class="decorative-shape shape-2"></div>
      <div class="decorative-shape shape-3"></div>
      
      <div class="relative z-10">
        <div class="mb-8">
          <svg class="w-24 h-24 mx-auto text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <h2 class="text-3xl font-bold mb-6 text-center">
          Password Recovery
        </h2>
        <p class="text-lg leading-relaxed mb-6 text-center">
          Don't worry! It happens to the best of us. Enter your email address and we'll send you a link to reset your password.
        </p>
        <div class="bg-white bg-opacity-10 rounded-lg p-6 backdrop-blur-sm">
          <h3 class="font-semibold mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            What happens next?
          </h3>
          <ul class="space-y-2 text-sm">
            <li class="flex items-start">
              <span class="text-green-300 mr-2">1.</span>
              <span>We'll send a reset link to your email</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-300 mr-2">2.</span>
              <span>Click the link in the email</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-300 mr-2">3.</span>
              <span>Create a new password</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-300 mr-2">4.</span>
              <span>Login with your new password</span>
            </li>
          </ul>
        </div>
      </div>
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

      // Forgot password form with loading state
      const forgotPasswordForm = document.getElementById('forgotPasswordForm');
      const sendResetBtn = document.getElementById('sendResetBtn');
      
      if (forgotPasswordForm && sendResetBtn) {
        forgotPasswordForm.addEventListener('submit', function(e) {
          const emailInput = document.getElementById('email');
          
          if (!emailInput.value.trim()) {
            e.preventDefault();
            showError(emailInput, 'Email is required');
            return;
          } else if (!isValidEmail(emailInput.value)) {
            e.preventDefault();
            showError(emailInput, 'Please enter a valid email address');
            return;
          } else {
            clearError(emailInput);
          }
          
          sendResetBtn.disabled = true;
          sendResetBtn.innerHTML = '<span class="animate-pulse">Sending...</span>';
          Notifications.loading('Sending reset link...');
        });
      }
    });

    // Form validation
    const emailInput = document.getElementById('email');

    function isValidEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
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