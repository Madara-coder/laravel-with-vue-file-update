<template>
    <div class="files-container">
      <header>
        <h1>File Management</h1>
        <button @click="logout" class="logout-btn">Logout</button>
      </header>
  
      <div class="upload-section">
        <input
          type="file"
          ref="fileInput"
          @change="handleFileSelect"
          style="display: none"
        />
        <button @click="$refs.fileInput.click()" class="upload-btn">
          📁 Upload File
        </button>
      </div>
  
      <div class="sort-section">
        <button @click="files.setSorting('original_name')">
          Sort by Name
          {{ files.sortBy === 'original_name' ? (files.sortDirection === 'asc' ? '↑' : '↓') : '' }}
        </button>
        <button @click="files.setSorting('size')">
          Sort by Size
          {{ files.sortBy === 'size' ? (files.sortDirection === 'asc' ? '↑' : '↓') : '' }}
        </button>
        <button @click="files.setSorting('created_at')">
          Sort by Date
          {{ files.sortBy === 'created_at' ? (files.sortDirection === 'asc' ? '↑' : '↓') : '' }}
        </button>
      </div>
  
      <div class="files-grid">
        <div v-for="file in files.files" :key="file.id" class="file-card">
          <div class="file-icon">📄</div>
          <h3>{{ file.original_name }}</h3>
          <p>{{ formatSize(file.size) }}</p>
          <p>{{ formatDate(file.created_at) }}</p>
          <div class="file-actions">
            <button @click="downloadFile(file.id)" class="btn-download">
              ⬇️
            </button>
            <button @click="shareFile(file.id)" class="btn-share">🔗</button>
            <button @click="deleteFile(file.id)" class="btn-delete">🗑️</button>
          </div>
        </div>
      </div>
  
      <div v-if="shareUrl" class="share-modal" @click="shareUrl = null">
        <div class="share-content" @click.stop>
          <h3>Share Link</h3>
          <input :value="shareUrl" readonly />
          <button @click="copyToClipboard">Copy</button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuthStore } from '@/stores/auth'
  import { useFileStore } from '@/stores/files'
  
  const router = useRouter()
  const auth = useAuthStore()
  const files = useFileStore()
  const shareUrl = ref(null)
  
  onMounted(() => {
    files.fetchFiles()
  })
  
  const handleFileSelect = async (e) => {
    const file = e.target.files[0]
    if (file) {
      await files.uploadFile(file)
      e.target.value = ''
    }
  }
  
  const downloadFile = (id) => files.downloadFile(id)
  
  const deleteFile = async (id) => {
    if (confirm('Delete this file?')) await files.deleteFile(id)
  }
  
  const shareFile = async (id) => {
    shareUrl.value = await files.shareFile(id)
  }
  
  const copyToClipboard = () => {
    navigator.clipboard.writeText(shareUrl.value)
    alert('Link copied!')
    shareUrl.value = null
  }
  
  const logout = async () => {
    await auth.logout()
    router.push('/login')
  }
  
  const formatSize = (bytes) => {
    if (bytes < 1024) return bytes + ' B'
    if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' KB'
    return (bytes / 1048576).toFixed(2) + ' MB'
  }
  
  const formatDate = (date) => new Date(date).toLocaleDateString()
  </script>
  
  <style scoped>
  .files-container {
    min-height: 100vh;
    background: #f7fafc;
    padding: 2rem;
  }
  
  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }
  
  .logout-btn {
    padding: 0.5rem 1rem;
    background: #e53e3e;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .upload-section {
    margin-bottom: 2rem;
  }
  
  .upload-btn {
    padding: 1rem 2rem;
    background: #48bb78;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
  }
  
  .sort-section {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
  }
  
  .sort-section button {
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
  }
  
  .file-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
  }
  
  .file-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
  }
  
  .file-actions {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1rem;
  }
  
  .file-actions button {
    padding: 0.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.2rem;
  }
  
  .btn-download {
    background: #4299e1;
  }
  .btn-share {
    background: #48bb78;
  }
  .btn-delete {
    background: #f56565;
  }
  
  .share-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .share-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    min-width: 400px;
  }
  
  .share-content input {
    width: 100%;
    padding: 0.75rem;
    margin: 1rem 0;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  .share-content button {
    width: 100%;
    padding: 0.75rem;
    background: #4299e1;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  @media (max-width: 768px) {
    .files-grid {
      grid-template-columns: 1fr;
    }
  }
  </style>