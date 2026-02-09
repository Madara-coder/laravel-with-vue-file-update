import { defineStore } from 'pinia'
import api from '@/services/api'

export const useFileStore = defineStore('files', {
  state: () => ({
    files: [],
    sortBy: 'created_at',
    sortDirection: 'desc'
  }),

  actions: {
    async fetchFiles() {
      const res = await api.get('/files', {
        params: { sort_by: this.sortBy, sort_direction: this.sortDirection }
      })
      this.files = res.data
    },

    async uploadFile(file) {
      const formData = new FormData()
      formData.append('file', file)
      const res = await api.post('/files', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      this.files.unshift(res.data)
      return res.data
    },

    async downloadFile(fileId) {
      const res = await api.get(`/files/${fileId}/download`, {
        responseType: 'blob'
      })
      const url = window.URL.createObjectURL(new Blob([res.data]))
      const link = document.createElement('a')
      link.href = url
      const fileName = res.headers['content-disposition']
        ?.split('filename=')[1]
        ?.replace(/"/g, '') || 'download'
      link.setAttribute('download', fileName)
      document.body.appendChild(link)
      link.click()
      link.remove()
    },

    async deleteFile(fileId) {
      await api.delete(`/files/${fileId}`)
      this.files = this.files.filter((f) => f.id !== fileId)
    },

    async shareFile(fileId) {
      const res = await api.post(`/files/${fileId}/share`)
      return res.data.share_url
    },

    setSorting(field) {
      if (this.sortBy === field) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortBy = field
        this.sortDirection = 'asc'
      }
      this.fetchFiles()
    }
  }
})