<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password - EZ Fitness</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    .logo-container {
      width: 120px;
      height: 120px;
      margin: 0 auto 1.5rem;
    }
    .logo-container img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
  </style>
</head>

<body class="flex items-center justify-center min-h-screen">
  <main class="w-full max-w-md bg-white shadow-2xl rounded-2xl p-8">
    <header class="mb-6 text-center">
      <div class="logo-container">
         <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" alt="EZ Fitness Logo" />
      </div>
      <h1 class="text-2xl font-bold text-gray-800">
        <span class="text-red-600">EZ</span> FITNESS GYM
      </h1>
      <p class="text-gray-600 mt-2 text-sm">Reset Your Password</p>
    </header>

    <!-- Alert Messages -->
    <section class="space-y-3">
      @if(session('error'))
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50"
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
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50"
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
    </section>

    <div class="mb-6 text-center text-sm text-gray-600">
      <p>Enter your email address and we'll send you a link to reset your password.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
      @csrf

      <section>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="your.email@example.com"
          class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" 
          required />
        @error('email')
          <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
        @enderror
      </section>

      <section>
        <button type="submit"
          class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-colors">
          Send Password Reset Link
        </button>
      </section>

      <footer class="text-center mt-4 text-sm">
        <p class="text-gray-600">Remember your password?
          <a href="{{ route('loginForm') }}" class="text-blue-600 hover:underline font-medium">Back to Login</a>
        </p>
      </footer>
    </form>
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