<template>
  <div class="bg-white text-dark min-vh-100 py-4 px-3">
    <div class="container-fluid bg-light rounded shadow p-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h3 class="mb-2 mb-lg-0">📁 Arquivos Processados</h3>
        <div>
          <input ref="inputArquivo" type="file" accept=".csv,.txt,.json,.xml" class="d-none" @change="enviarArquivo" />
          <button class="btn btn-primary" @click="lerArquivo">
            <i class="fas fa-upload me-2"></i> Ler Arquivo
          </button>
        </div>
      </div>

      <input v-model="filtro" placeholder="🔍 Buscar por nome..." class="form-control mb-3" />

      <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Nome do Arquivo</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="arquivo in arquivosFiltrados" :key="arquivo.id">
            <td>{{ arquivo.nome || arquivo.nome_arquivo }}</td>
            <td><span class="badge bg-success">✔️ Pronto</span></td>
            <td>
              <button class="btn btn-outline-secondary btn-sm me-2" @click="visualizar(arquivo.id)">👁 Ver</button>
            </td>
          </tr>
        </tbody>
      </table>

      <BModal v-model:visible="modalVisivel" title="Conteúdo do Arquivo">
        <pre class="bg-dark text-white p-3 rounded small">{{ JSON.stringify(dados, null, 2) }}</pre>
      </BModal>


    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'

const arquivos = ref([])
const dados = ref(null)
const filtro = ref('')
const inputArquivo = ref(null)

const carregarArquivos = async () => {
  try {
    const response = await api.get('/files')
    arquivos.value = response.data.map(item => ({ id: item.id, nome: item.nome_arquivo }))
  } catch (e) {
    console.error(e)
    Swal.fire('Erro', 'Não foi possível carregar os arquivos', 'error')
  }
}

const arquivosFiltrados = computed(() => {
  if (!filtro.value) return arquivos.value
  return arquivos.value.filter(a =>
    (a.nome || a.nome_arquivo).toLowerCase().includes(filtro.value.toLowerCase())
  )

})

const modalVisivel = ref(false)

const visualizar = async (id) => {
  try {
    const response = await api.get(`/files/${id}`)
    dados.value = response.data
    modalVisivel.value = true
  } catch (e) {
    console.error('Erro ao carregar dados:', e)
    Swal.fire('Erro', 'Falha ao carregar dados', 'error')
  }
}


const lerArquivo = () => {
  inputArquivo.value?.click()
}

const enviarArquivo = async (e) => {
  const file = e.target.files[0]
  if (!file) return
  const formData = new FormData()
  formData.append('file', file)
  try {
    await api.post('/processed-records', formData)
    Swal.fire('Sucesso', 'Arquivo enviado e processado com sucesso!', 'success')
    await carregarArquivos()
  } catch {
    Swal.fire('Erro', 'Erro ao enviar o arquivo.', 'error')
  }
}

onMounted(carregarArquivos)
</script>

<style scoped>
.table td,
.table th {
  vertical-align: middle;
}
</style>
