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
