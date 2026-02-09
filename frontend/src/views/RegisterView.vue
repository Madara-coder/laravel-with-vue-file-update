<template>
    <div class="auth-container">
      <div class="auth-card">
        <h2>Register</h2>
        <form @submit.prevent="handleRegister">
          <input v-model="form.name" type="text" placeholder="Name" required />
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
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Confirm Password"
            required
          />
          <button type="submit">Register</button>
          <p class="error" v-if="error">{{ error }}</p>
        </form>
        <p>
          Already have an account?
          <router-link to="/login">Login</router-link>
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
  const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })
  
  const handleRegister = async () => {
    try {
      await auth.register(form.value)
      router.push('/')
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
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