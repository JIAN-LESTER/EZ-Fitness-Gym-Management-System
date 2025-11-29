<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - EZ Fitness</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
<body class="flex items-center justify-center min-h-screen py-8">
  <main class="w-full max-w-md bg-white shadow-2xl rounded-2xl p-8">
    <header class="mb-6 text-center">
      <div class="logo-container">
       <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" alt="EZ Fitness Logo" />
      </div>
      <h1 class="text-2xl font-bold text-gray-800">
        <span class="text-red-600">EZ</span> FITNESS GYM
      </h1>
    </header>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
      @csrf

      <section class="grid grid-cols-2 gap-3">
        <div>
          <input type="text" id="first_name" name="first_name" placeholder="Firstname"
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" />
          @error('first_name')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
          @enderror
        </div>
        <div>
          <input type="text" id="last_name" name="last_name" placeholder="Firstname"
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" />
          @error('last_name')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
          @enderror
        </div>
      </section>

      <section>
        <input type="text" id="username" name="username" placeholder="Username"
          class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" />
        @error('username')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
      </section>

      <section>
        <input type="email" id="email" name="email" placeholder="Email"
          class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" />
        @error('email')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
      </section>

      <section class="relative">
        <input type="password" id="password" name="password" placeholder="Password"
          class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" />
        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
          <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
        </button>
        @error('password')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
      </section>

      <section class="relative">
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"
          class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none" />
        <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
          <svg id="eyeIconConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
        </button>
        @error('password_confirmation')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
      </section>

      <section class="pt-2">
        <button type="submit"
          class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-colors">
          Create Account
        </button>
      </section>

      <footer class="text-center mt-4 text-sm">
        <p class="text-gray-600">Already have an Account?
          <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Login</a>
        </p>
      </footer>
    </form>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Toast notifications - Only show errors, skip success messages
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

    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);

      if (type === 'text') {
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
      } else {
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
      }
    });

    // Toggle confirm password visibility
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const eyeIconConfirm = document.getElementById('eyeIconConfirm');

    toggleConfirmPassword.addEventListener('click', function() {
      const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordInput.setAttribute('type', type);

      if (type === 'text') {
        eyeIconConfirm.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
      } else {
        eyeIconConfirm.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
      }
    });

    // Form validation
    const form = document.querySelector('form');
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      let isValid = true;

      // First name
      if (!firstNameInput.value.trim()) {
        showError(firstNameInput, 'First name is required');
        isValid = false;
      } else {
        clearError(firstNameInput);
      }

      // Last name
      if (!lastNameInput.value.trim()) {
        showError(lastNameInput, 'Last name is required');
        isValid = false;
      } else {
        clearError(lastNameInput);
      }

      // Username
      if (!usernameInput.value.trim()) {
        showError(usernameInput, 'Username is required');
        isValid = false;
      } else if (usernameInput.value.length < 4) {
        showError(usernameInput, 'Username must be at least 4 characters');
        isValid = false;
      } else {
        const usernameTaken = await checkIfTaken('{{ route('check.username') }}', 'username', usernameInput.value);
        if (usernameTaken) {
          showError(usernameInput, 'Username is already taken');
          isValid = false;
        } else {
          clearError(usernameInput);
        }
      }

      // Email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailInput.value.trim()) {
        showError(emailInput, 'Email is required');
        isValid = false;
      } else if (!emailRegex.test(emailInput.value)) {
        showError(emailInput, 'Please enter a valid email address');
        isValid = false;
      } else {
        const emailTaken = await checkIfTaken('{{ route('check.email') }}', 'email', emailInput.value);
        if (emailTaken) {
          showError(emailInput, 'Email is already taken');
          isValid = false;
        } else {
          clearError(emailInput);
        }
      }

      // Password
      if (!passwordInput.value.trim()) {
        showError(passwordInput, 'Password is required');
        isValid = false;
      } else if (passwordInput.value.length < 6) {
        showError(passwordInput, 'Password must be at least 6 characters');
        isValid = false;
      } else {
        clearError(passwordInput);
      }

      // Confirm Password
      if (!confirmPasswordInput.value.trim()) {
        showError(confirmPasswordInput, 'Password confirmation is required');
        isValid = false;
      } else if (confirmPasswordInput.value !== passwordInput.value) {
        showError(confirmPasswordInput, 'Password confirmation does not match');
        isValid = false;
      } else {
        clearError(confirmPasswordInput);
      }

      if (!isValid) return;
      form.submit();
    });

    async function checkIfTaken(url, param, value) {
      try {
        const res = await fetch(`${url}?${param}=${encodeURIComponent(value)}`);
        const data = await res.json();
        return data.taken;
      } catch (err) {
        console.error('Check failed:', err);
        return false;
      }
    }

    function showError(input, message) {
      input.classList.add('border-red-500');
      input.classList.remove('border-gray-300');

      // Add shake animation
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
