<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
          <input type="text" id="first_name" name="first_name" required
            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
        </div>
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
          <input type="text" id="last_name" name="last_name" required
            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
        </div>
      </section>

      <section>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" id="username" name="username" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
      </section>

      <section>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
      </section>

      <section>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
      </section>

      <section>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-600 focus:outline-none" />
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
</body>
</html>
