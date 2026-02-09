<template>
    <div class="auth-container">
      <div class="auth-card">
        <h2>Login</h2>
        <form @submit.prevent="handleLogin">
          <input
            v-model="form.email"
            type="email"
            placeholder="Email"
            required
          />
          <input
            v-model="form.password"
            type="password"
            placeholder="Password"
            required
          />
          <button type="submit">Login</button>
          <p class="error" v-if="error">{{ error }}</p>
        </form>
        <p>
          Don't have an account?
          <router-link to="/register">Register</router-link>
        </p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuthStore } from '@/stores/auth'
  
  const router = useRouter()
  const auth = useAuthStore()
  const error = ref('')
  const form = ref({ email: '', password: '' })
  
  const handleLogin = async () => {
    try {
      await auth.login(form.value)
      router.push('/')
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
    }
  }
  </script>
  
  <style scoped>
  .auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }
  
  .auth-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
  }
  
  h2 {
    margin-bottom: 1.5rem;
    text-align: center;
  }
  
  form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
  }
  
  button {
    padding: 0.75rem;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
  }
  
  button:hover {
    background: #5568d3;
  }
  
  .error {
    color: #e53e3e;
    font-size: 0.875rem;
  }
  
  p {
    margin-top: 1rem;
    text-align: center;
  }
  </style>