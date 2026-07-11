<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EZ Fitness</title>
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
      top: 10%; left: -50px;
      transform: rotate(45deg);
    }

    .shape-2 {
      width: 150px; height: 150px;
      background: linear-gradient(45deg, #feca57, #ff6b6b);
      bottom: 20%; left: 10%;
      transform: rotate(-30deg);
    }

    .shape-3 {
      width: 100px; height: 100px;
      background: linear-gradient(45deg, #ff9ff3, #feca57);
      top: 50%; left: 30%;
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

    /* Validation state icons */
    .field-valid .validation-icon-ok { display: block; }
    .field-valid .validation-icon-err { display: none; }
    .field-invalid .validation-icon-ok { display: none; }
    .field-invalid .validation-icon-err { display: block; }
    .validation-icon-ok, .validation-icon-err { display: none; }

    /* Spinner for async checks */
    .checking-spinner {
      display: none;
      width: 16px; height: 16px;
      border: 2px solid #d1d5db;
      border-top-color: #6b7280;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
    }

    .is-checking .checking-spinner { display: block; }
    .is-checking .validation-icon-ok,
    .is-checking .validation-icon-err { display: none; }

    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>

<body class="flex items-center bg-gray-300 justify-center min-h-screen p-4 py-8">
  <main class="w-full max-w-6xl bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col md:flex-row min-h-[700px]">

    <!-- Left Side -->
    <section class="hidden md:flex md:w-1/2 info-section text-white p-12 flex-col justify-center relative">
      <div class="decorative-shape shape-1"></div>
      <div class="decorative-shape shape-2"></div>
      <div class="decorative-shape shape-3"></div>

      <div class="relative z-10">
        <h2 class="text-4xl font-bold mb-6">
          <span class="text-red-400">EZ</span> FITNESS GYM
        </h2>
        <h3 class="text-2xl font-semibold mb-4">Digitalize Your Membership</h3>
        <p class="text-lg leading-relaxed mb-6">
          Create an account to access your digital gym membership. Members can check in using QR codes and view gym occupancy, while administrators manage records, inventory, and point-of-sale transactions efficiently.
        </p>
        <ul class="space-y-3">
          <li class="flex items-center">
            <svg class="w-6 h-6 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Digital Membership Access
          </li>
          <li class="flex items-center">
            <svg class="w-6 h-6 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Real-Time Gym Occupancy Viewing
          </li>
          <li class="flex items-center">
            <svg class="w-6 h-6 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            View Dashboard
          </li>
          <li class="flex items-center">
            <svg class="w-6 h-6 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Attendance Logs for Members
          </li>
        </ul>
      </div>
    </section>

    <!-- Right Side - Registration Form -->
    <section class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
      <header class="mb-8">
        <div class="flex items-center justify-center md:justify-start mb-4">
          <div class="w-16 h-16">
            <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" alt="EZ Fitness Logo" class="w-full h-full object-contain" />
          </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 text-center md:text-left">CREATE ACCOUNT</h1>
        <p class="text-gray-600 mt-2 text-center md:text-left">Sign up to get started with EZ Fitness</p>
      </header>

      <form id="registerForm" method="POST" action="/register" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
            <input type="text" id="first_name" name="first_name" placeholder="John"
              class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none"
              value="{{ old('first_name') }}" />
            @error('first_name')
              <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
          </div>
          <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
            <input type="text" id="last_name" name="last_name" placeholder="Doe"
              class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none"
              value="{{ old('last_name') }}" />
            @error('last_name')
              <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <!-- Username with real-time check -->
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <div class="relative">
            <input type="text" id="username" name="username" placeholder="johndoe123"
              class="w-full border border-gray-300 rounded-lg focus:ring-2 p-3 focus:ring-gray-400 focus:border-transparent focus:outline-none"
              value="{{ old('username') }}" autocomplete="username" />
 
          </div>
          @error('username')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
          @enderror
          <span class="error-message text-red-500 text-xs mt-1 hidden" id="username-error"></span>
        </div>

        <!-- Email with real-time check -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
          <div class="relative">
            <input id="email" name="email" placeholder="john@example.com" novalidate
              class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none"
              value="{{ old('email') }}" autocomplete="email" />
            
          </div>
          @error('email')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
          @enderror
          <span class="error-message text-xs mt-1 hidden" id="email-error"></span>
          <p class="flex items-center gap-1 text-xs text-amber-600 mt-1" id="email-hint">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            Please use a valid email address — a verification link will be sent to activate your account.
          </p>
        </div>

        <div class="relative">
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" id="password" name="password" placeholder="••••••••"
            class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none"
            autocomplete="new-password" />
          <button type="button" id="togglePassword" class="absolute right-3 top-[40px] text-gray-500 hover:text-gray-700">
            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
          @error('password')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
          @enderror
        </div>

        <div class="relative">
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••"
            class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-gray-400 focus:border-transparent focus:outline-none"
            autocomplete="new-password" />
          <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-[40px] text-gray-500 hover:text-gray-700">
            <svg id="eyeIconConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
          @error('password_confirmation')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
          @enderror
        </div>

        <div class="pt-2">
          <button type="submit" id="registerBtn"
            class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-all shadow-lg hover:shadow-xl">
            CREATE ACCOUNT
          </button>
        </div>

        <p class="text-center text-sm text-gray-600 mt-4">
          Already have an account?
          <a href="{{ route('login') }}" class="text-gray-800 hover:underline font-medium">Login</a>
        </p>
      </form>
    </section>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/notifications.js') }}"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      // ── Session toasts ──────────────────────────────────────────────────────
      @if(session('success')) Notifications.toast('success', '{{ session('success') }}'); @endif
      @if(session('error'))   Notifications.toast('error',   '{{ session('error') }}');   @endif
      @if(session('warning')) Notifications.toast('warning', '{{ session('warning') }}'); @endif
      @if(session('info'))    Notifications.toast('info',    '{{ session('info') }}');    @endif
      @if(session('status'))  Notifications.toast('success', '{{ session('status') }}'); @endif

      // ── Element refs ────────────────────────────────────────────────────────
      const form                 = document.getElementById('registerForm');
      const firstNameInput       = document.getElementById('first_name');
      const lastNameInput        = document.getElementById('last_name');
      const usernameInput        = document.getElementById('username');
      const emailInput           = document.getElementById('email');
      const passwordInput        = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('password_confirmation');
      const registerBtn          = document.getElementById('registerBtn');

      // ── Validation cache (avoids duplicate network hits) ────────────────────
      // Structure: { value: string, taken: boolean }
      const cache = { username: null, email: null };

      // ── Single combined endpoint ─────────────────────────────────────────────
      // Sends BOTH fields in one request when checking on submit.
      // Falls back to individual checks for real-time blur events.
      const CHECK_URL = '/check-availability'; // NEW combined route (see AuthController)

      async function checkField(field, value) {
        // Return cached result if value hasn't changed
        if (cache[field] && cache[field].value === value) {
          return cache[field].taken;
        }
        try {
          const res  = await fetch(`${CHECK_URL}?field=${field}&value=${encodeURIComponent(value)}`);
          const data = await res.json();
          cache[field] = { value, taken: data.taken };
          return data.taken;
        } catch {
          return false; // fail open — server validation is the safety net
        }
      }

      // ── Debounce helper ──────────────────────────────────────────────────────
      function debounce(fn, ms) {
        let timer;
        return function (...args) {
          clearTimeout(timer);
          timer = setTimeout(() => fn.apply(this, args), ms);
        };
      }

      // ── UI helpers ───────────────────────────────────────────────────────────
      function setStatus(statusEl, state) {
        // state: 'checking' | 'ok' | 'error' | 'idle'
        statusEl.classList.remove('is-checking', 'field-valid', 'field-invalid');
        if (state === 'checking') statusEl.classList.add('is-checking');
        if (state === 'ok')       statusEl.classList.add('field-valid');
        if (state === 'error')    statusEl.classList.add('field-invalid');
      }

      function showError(input, message, errorElId) {
        input.classList.add('border-red-500');
        input.classList.remove('border-gray-300', 'border-green-400');
        input.style.animation = 'shake 0.5s';
        setTimeout(() => { input.style.animation = ''; }, 500);

        if (errorElId) {
          const el = document.getElementById(errorElId);
          if (el) { el.textContent = message; el.classList.remove('hidden'); }
        } else {
          let span = input.parentNode.querySelector('.error-message');
          if (!span) {
            span = document.createElement('span');
            span.className = 'error-message text-red-500 text-xs mt-1 block';
            input.parentNode.appendChild(span);
          }
          span.textContent = message;
        }
      }

      function clearError(input, errorElId) {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');

        if (errorElId) {
          const el = document.getElementById(errorElId);
          if (el) { el.textContent = ''; el.classList.add('hidden'); }
        } else {
          const span = input.parentNode.querySelector('.error-message');
          if (span) span.remove();
        }
      }

    


      const debouncedUsernameCheck = debounce(validateUsernameAsync, 400);
      usernameInput.addEventListener('input', (e) => debouncedUsernameCheck(e.target.value));
      usernameInput.addEventListener('blur',  (e) => validateUsernameAsync(e.target.value));

      // Email
      const emailRegex     = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
   
      const emailHint      = document.getElementById('email-hint');
      const emailErrorEl   = document.getElementById('email-error');

  

      const debouncedEmailCheck = debounce(validateEmailAsync, 400);
      emailInput.addEventListener('input', (e) => debouncedEmailCheck(e.target.value));
      emailInput.addEventListener('blur',  (e) => validateEmailAsync(e.target.value));

      // ── Form submit ──────────────────────────────────────────────────────────
      if (form) {
        form.addEventListener('submit', async function (e) {
          e.preventDefault();
          let isValid = true;

          // --- Sync validations (instant, no network) ---
          if (!firstNameInput.value.trim()) {
            showError(firstNameInput, 'First name is required');
            isValid = false;
          } else {
            clearError(firstNameInput);
          }

          if (!lastNameInput.value.trim()) {
            showError(lastNameInput, 'Last name is required');
            isValid = false;
          } else {
            clearError(lastNameInput);
          }

          const usernameVal = usernameInput.value.trim();
          if (!usernameVal) {
            showError(usernameInput, 'Username is required', 'username-error');
            isValid = false;
          } else if (usernameVal.length < 4) {
            showError(usernameInput, 'Username must be at least 4 characters', 'username-error');
            isValid = false;
          }

          const emailVal = emailInput.value.trim();
          if (!emailVal) {
            showError(emailInput, 'Email is required', 'email-error');
            isValid = false;
          } else if (!emailRegex.test(emailVal)) {
            showError(emailInput, 'Please enter a valid email address', 'email-error');
            isValid = false;
          }

          if (!passwordInput.value.trim()) {
            showError(passwordInput, 'Password is required');
            isValid = false;
          } else if (passwordInput.value.length < 6) {
            showError(passwordInput, 'Password must be at least 6 characters');
            isValid = false;
          } else {
            clearError(passwordInput);
          }

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

         
          const [usernameTaken, emailTaken] = await Promise.all([
            (usernameVal && usernameVal.length >= 4) ? checkField('username', usernameVal) : Promise.resolve(false),
            (emailVal && emailRegex.test(emailVal))  ? checkField('email', emailVal)       : Promise.resolve(false),
          ]);

          if (usernameTaken) {
            showError(usernameInput, 'Username is already taken', 'username-error');
     
            isValid = false;
          }

          if (emailTaken) {
            showError(emailInput, 'Email is already taken', 'email-error');
       
            isValid = false;
          }

          if (!isValid) return;

          // --- All good: submit ---
          registerBtn.disabled = true;
          registerBtn.innerHTML = '<span class="animate-pulse">Creating account...</span>';
          Notifications.loading('Creating your account...');
          form.submit();
        });
      }

      // ── Password visibility toggles ─────────────────────────────────────────
      const EYE_OPEN  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
      const EYE_CLOSE = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';

      function toggleVisibility(btn, input, iconEl) {
        if (!btn || !input || !iconEl) return;
        btn.addEventListener('click', function () {
          const show = input.type === 'password';
          input.type = show ? 'text' : 'password';
          iconEl.innerHTML = show ? EYE_CLOSE : EYE_OPEN;
        });
      }

      toggleVisibility(
        document.getElementById('togglePassword'),
        passwordInput,
        document.getElementById('eyeIcon')
      );
      toggleVisibility(
        document.getElementById('toggleConfirmPassword'),
        confirmPasswordInput,
        document.getElementById('eyeIconConfirm')
      );
    });
  </script>
</body>
</html>
