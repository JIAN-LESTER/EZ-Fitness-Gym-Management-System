<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password - EZ Fitness</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
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

      <!-- Alert Messages -->
      <div class="space-y-3 mb-6">
        @if(session('error'))
          <div class="flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50"
            role="alert">
            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <div>
              <span class="font-medium">Error:</span> {{ session('error') }}
            </div>
          </div>
        @endif

        @if(session('status'))
          <div class="flex items-center p-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50"
            role="alert">
            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
            </svg>
            <div>
              {{ session('status') }}
            </div>
          </div>
        @endif
      </div>

      <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
              <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
            </svg>
            Email Address
          </label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="your.email@example.com"
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" 
            required />
          @error('email')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit"
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

  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <script>
    @if(session('error'))
      Toastify({
        text: "{{ session('error') }}",
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
        stopOnFocus: true,
      }).showToast();
    @endif

    @if(session('status'))
      Toastify({
        text: "{{ session('status') }}",
        duration: 4000,
        gravity: "top",
        position: "right",
        backgroundColor: "linear-gradient(to right, #10b981, #059669)",
        stopOnFocus: true,
      }).showToast();
    @endif

    // Form validation
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');

    form.addEventListener('submit', function(e) {
      if (!emailInput.value.trim()) {
        e.preventDefault();
        showError(emailInput, 'Email is required');
      } else if (!isValidEmail(emailInput.value)) {
        e.preventDefault();
        showError(emailInput, 'Please enter a valid email address');
      } else {
        clearError(emailInput);
      }
    });

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