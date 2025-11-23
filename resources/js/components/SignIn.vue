<template>
  <div class="min-h-screen flex items-center justify-center p-4" style="background: linear-gradient(to bottom right, #f97316, #ea580c);">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden max-w-4xl w-full flex">
      <div class="w-1/2 p-12 text-white text-center" style="background: linear-gradient(to bottom right, #0d9488, #0f766e);">
        <h3 class="text-3xl font-bold mb-2">Hello Welcome</h3>
        <p class="mb-8">No account yet</p>
        <a href="/register" class="inline-block bg-white px-8 py-3 rounded-lg" style="color: #0f766e;">Sign Up</a>
      </div>
      <div class="w-1/2 p-12">
        <h2 class="text-3xl font-bold mb-8">Login</h2>
        <form class="space-y-6">
          <input type="text" placeholder="Username" class="w-full px-4 py-3 bg-gray-100 rounded-lg" />
          <input type="email" placeholder="Email" class="w-full px-4 py-3 bg-gray-100 rounded-lg" />
          <input type="password" placeholder="Password" class="w-full px-4 py-3 bg-gray-100 rounded-lg" />
          <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg">Sign In</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>