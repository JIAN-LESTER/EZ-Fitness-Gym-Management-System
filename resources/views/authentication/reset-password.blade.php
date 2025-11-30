<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password - EZ Fitness</title>
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
      <p class="text-gray-600 mt-2 text-sm">Create New Password</p>
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
    </section>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5 mt-6">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <section>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
        <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" 
          class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none bg-gray-50" 
          required readonly />
        @error('email')
          <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
        @enderror
      </section>

      <section class="relative">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
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
        <p class="text-xs text-gray-500 mt-1">Password must be at least 6 characters</p>
      </section>

      <section class="relative">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password"
          class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" 
          required />
        <button type="button" id="togglePasswordConfirm" class="absolute right-3 top-[42px] text-gray-500 hover:text-gray-700">
          <svg id="eyeIconConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
        </button>
      </section>

      <section>
        <button type="submit"
          class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-colors">
          Reset Password
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

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const eyeIconConfirm = document.getElementById('eyeIconConfirm');

    togglePassword.addEventListener('click', function() {
      togglePasswordVisibility(passwordInput, eyeIcon);
    });

    togglePasswordConfirm.addEventListener('click', function() {
      togglePasswordVisibility(passwordConfirmInput, eyeIconConfirm);
    });

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
    const form = document.querySelector('form');

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
      }
    });

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