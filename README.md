# 📦 Projeto Fullstack Laravel + Vue - Processamento de Arquivos

Este projeto permite o upload e processamento de arquivos estruturados (`.csv`, `.txt`, `.json`, `.xml`) no back-end Laravel, com exibição dos dados processados em uma interface moderna feita com Vue 3, Bootstrap e SweetAlert2.

---

## 🧱 Estrutura do Projeto

```
.
├── backend/ (Laravel 12 API)
└── frontend/ (Vue 3 com Vite)
```

---

## 🚀 Funcionalidades

### ✅ Backend (Laravel)

- Upload de arquivos via `POST /api/processed-records`
- Salvamento dos dados em JSON no banco
- Endpoint para listar arquivos: `GET /api/files`
- Endpoint para visualizar conteúdo de um arquivo: `GET /api/files/{id}`
- Documentação Swagger em `/api/documentation`

### ✅ Frontend (Vue 3)

- Upload de arquivos com feedback de sucesso ou erro
- Listagem de arquivos com busca
- Modal para visualizar dados do arquivo em JSON formatado
- Integração com `bootstrap-vue-next`, `sweetalert2` e `axios`

---

## ⚙️ Como rodar o projeto

### 🔧 Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- Docker (opcional)
- MySQL ou PostgreSQL

### 🧪 Setup do Back-end (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Configure conexão com banco no .env
php artisan migrate
php artisan serve --port=9584
```

### 🌐 Setup do Front-end (Vue)

```bash
cd frontend
npm install
npm run dev -- --port 5177
```

> O front estará em: http://localhost:5177  
> O back em: http://localhost:9584

---

## 🛠 Tecnologias

- **Laravel 12** (API REST)
- **Vue 3 + Vite**
- **Bootstrap 5 + bootstrap-vue-next**
- **SweetAlert2** para alertas elegantes
- **Axios** para requisições HTTP
- **Swagger** (OpenAPI) com `L5-Swagger`

---

## 🧪 Endpoints disponíveis

| Método | Rota                       | Descrição                      |
|--------|----------------------------|-------------------------------|
| POST   | `/api/processed-records`   | Upload e processamento        |
| GET    | `/api/files`               | Lista arquivos disponíveis    |
| GET    | `/api/files/{id}`          | Exibe conteúdo de um arquivo  |

---

## 💡 Observações importantes

- O modal do Bootstrap precisa que o `bootstrap` esteja disponível globalmente. Para isso, foi feito:

```js
import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap
```

- O back-end retorna os registros como array; o front trata corretamente com `dados.value = response.data`.

---

## 📝 Licença

Este projeto está sob a licença MIT. Sinta-se livre para usar, modificar e distribuir.