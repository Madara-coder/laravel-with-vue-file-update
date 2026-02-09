import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token')
  }),

  getters: {
    isAuthenticated: (state) => !!state.token
  },

  actions: {
    async register(data) {
      const res = await api.post('/register', data)
      this.token = res.data.token
      this.user = res.data.user
      localStorage.setItem('token', this.token)
    },

    async login(credentials) {
      const res = await api.post('/login', credentials)
      this.token = res.data.token
      this.user = res.data.user
      localStorage.setItem('token', this.token)
    },

    async logout() {
      await api.post('/logout')
      this.token = null
      this.user = null
      localStorage.removeItem('token')
    },

    async fetchUser() {
      if (this.token) {
        const res = await api.get('/me')
        this.user = res.data
      }
    }
  }
})