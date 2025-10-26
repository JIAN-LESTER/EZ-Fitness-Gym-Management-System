<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen">
  <main class="w-full max-w-md bg-white shadow-lg rounded-xl p-8">
    <header class="mb-6 text-center">
      <h1 class="text-2xl font-bold text-green-700">Sign In</h1>
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
        <input type="text" id="username" name="username" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
      </section>

      <section>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
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
</body>

</html>