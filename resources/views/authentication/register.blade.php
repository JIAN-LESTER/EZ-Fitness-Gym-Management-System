<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
  <main class="w-full max-w-lg bg-white shadow-lg rounded-xl p-8">
    <header class="mb-6 text-center">
      <h1 class="text-2xl font-bold text-green-700">Create Account</h1>
      <p class="text-gray-600 text-sm mt-1">Join and start managing your membership today.</p>
    </header>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
      @csrf

      <section class="grid grid-cols-2 gap-4">
        <div>
          <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
          <input type="text" id="first_name" name="first_name" 
            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
                    @error('first_name')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
        </div>
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
          <input type="text" id="last_name" name="last_name" 
            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
                    @error('last_name')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
        </div>
      </section>

      <section>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" id="username" name="username" 
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
                  @error('username')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
      </section>

      <section>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" 
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
                  @error('email')
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
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" 
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
                  @error('password_confirmation')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
      </section>

      <section>
        <button type="submit"
          class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-lg transition-colors">
          Register
        </button>
      </section>

      <footer class="text-center mt-4 text-sm">
        <p>Already have an account?
          <a href="{{ route('login') }}" class="text-green-700 hover:underline">Login</a>
        </p>
      </footer>
    </form>
  </main>

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
  const firstNameInput = document.getElementById('first_name');
  const lastNameInput = document.getElementById('last_name');
  const usernameInput = document.getElementById('username');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('password_confirmation');

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
</script>



</body>
</html>
