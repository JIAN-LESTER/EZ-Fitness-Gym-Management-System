<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen">
  <main class="w-full max-w-md bg-white shadow-lg rounded-xl p-8">
    <header class="mb-6 text-center">
      <h1 class="text-2xl font-bold text-green-700">EZ Fitness</h1>
      <p class="text-gray-600 text-sm mt-1">Welcome back! Please log in.</p>
    </header>

    <!-- Alert Messages -->
    <section class="space-y-3">

      {{-- Error Alert --}}
      @if(session('error') && session('error') !== 'Your email is not verified.')
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

      {{-- Success Alert --}}
      @if(session('success'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50"
          role="alert">
          <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            fill="currentColor" viewBox="0 0 20 20">
            <path
              d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
          </svg>
          <div>
            <span class="font-medium">Success:</span> {{ session('success') }}
          </div>
        </div>
      @endif

      {{-- Resend Verification (Warning - Orange) --}}
      @if(session('resend_user_id'))
        <div
          class="flex items-center justify-between p-4 mb-4 text-sm text-orange-800 border border-orange-300 rounded-lg bg-orange-50"
          role="alert">
          <div class="flex items-center">
            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <div>
              Your account is not yet verified.
            </div>
          </div>
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ session('resend_user_id') }}">
            <button type="submit"
              class="ml-3 px-3 py-1.5 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-400 focus:outline-none">
              Resend Email
            </button>
          </form>
        </div>
      @endif

    </section>



    <form method="POST" action="{{ route('login') }}" class="space-y-5">

      @csrf
      <section>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" id="username" name="username"
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
        @error('username')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
      </section>

      <section>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password"
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
        @error('password')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
      </section>

      <section>
        <button type="submit"
          class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-lg transition-colors">
          Login
        </button>
      </section>

      <footer class="text-center mt-4 text-sm">
        <p>Don't have an account?
          <a href="{{ route('register') }}" class="text-green-700 hover:underline">Register</a>
        </p>
      </footer>
    </form>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    @if(session('success'))
        Toastify({
            text: "{{ session('success') }}",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #10b981, #059669)",
            stopOnFocus: true,
        }).showToast();
    @endif

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

    @if($errors->any())
        Toastify({
            text: "{{ $errors->first() }}",
            duration: 4000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
            stopOnFocus: true,
        }).showToast();
    @endif
    </script>

    <script>
  const form = document.querySelector('form');
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');

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

      if (!isValid) {
          e.preventDefault();
      }
  });

  function showError(input, message) {
      input.classList.add('border-red-500');
      input.classList.remove('border-gray-300');

      let errorSpan = input.nextElementSibling;
      if (!errorSpan || !errorSpan.classList.contains('error-message')) {
          errorSpan = document.createElement('span');
          errorSpan.className = 'error-message text-red-500 text-xs mt-1 block';
          input.parentNode.appendChild(errorSpan);
      }
      errorSpan.textContent = message;
  }

  function clearError(input) {
      input.classList.remove('border-red-500');
      input.classList.add('border-gray-300');

      const errorSpan = input.nextElementSibling;
      if (errorSpan && errorSpan.classList.contains('error-message')) {
          errorSpan.remove();
      }
  }

  // Live validation for username
  usernameInput.addEventListener('blur', function() {
      if (!this.value.trim()) {
          showError(this, 'Username is required');
      } else if (this.value.length < 4) {
          showError(this, 'Username must be at least 4 characters');
      } else {
          clearError(this);
      }
  });

  // Live validation for password
  passwordInput.addEventListener('blur', function() {
      if (!this.value.trim()) {
          showError(this, 'Password is required');
      } else if (this.value.length < 6) {
          showError(this, 'Password must be at least 6 characters')
      } else {
          clearError(this);
      }
  });
</script>

</body>

</html>
