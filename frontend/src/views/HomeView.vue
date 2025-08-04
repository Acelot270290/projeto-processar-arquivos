<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const arquivos = ref([])
const dadosSelecionados = ref(null)
const fileInput = ref(null)

const carregarArquivos = async () => {
  try {
    const response = await axios.get('/api/files')
    arquivos.value = response.data
  } catch (error) {
    Swal.fire('Erro', 'Erro ao carregar arquivos', 'error')
  }
}

const uploadArquivo = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('file', file)

  try {
    await axios.post('/api/processar', formData)
    Swal.fire('Sucesso', 'Arquivo processado com sucesso', 'success')
    carregarArquivos()
  } catch (error) {
    Swal.fire('Erro', 'Erro ao processar o arquivo', 'error')
  }
}

const visualizarArquivo = async (nome) => {
  try {
    const response = await axios.get(`/api/files/${nome}`)
    dadosSelecionados.value = response.data
  } catch (error) {
    Swal.fire('Erro', 'Erro ao carregar dados do arquivo', 'error')
  }
}

onMounted(carregarArquivos)
</script>

<template>
  <div class="container py-4">
    <h2 class="mb-3">Upload de Arquivos</h2>
    <input type="file" @change="uploadArquivo" ref="fileInput" class="form-control mb-4" />

    <h3>Arquivos Processados</h3>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Nome do Arquivo</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="arquivo in arquivos" :key="arquivo">
          <td>{{ arquivo }}</td>
          <td>
            <button class="btn btn-sm btn-primary" @click="visualizarArquivo(arquivo)">
              <i class="fas fa-eye"></i> Visualizar
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="dadosSelecionados">
      <h4>Dados do Arquivo</h4>
      <pre>{{ JSON.stringify(dadosSelecionados, null, 2) }}</pre>
    </div>
  </div>
</template>

<style scoped>
.container {
  max-width: 800px;
}
</style>
