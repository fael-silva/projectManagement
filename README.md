# **📌 Documentação da API - Project Management**

API para gerenciamento de projetos e tarefas, incluindo autenticação, notificações, permissões e relatórios.

---

## **1️⃣ Funcionalidades da API**

### **🔐 1.1. Autenticação**
- **Login com JWT:** Permite autenticação via token JWT.
- **Proteção de rotas:** Middleware `jwt.auth` protege endpoints sensíveis.

### **📂 1.2. Gerenciamento de Projetos**
- Criar, listar, atualizar e excluir projetos.
- Vincular tarefas aos projetos durante a criação/edição.
- Atualização automática de datas:
  - `end_date` ao concluir um projeto.
  - `completed_at` ao concluir uma tarefa.

### **📊 1.3. Relatórios**
- Métricas gerais sobre projetos e tarefas:
  - Número total de projetos.
  - Projetos por status: `planejado`, `em andamento`, `concluído`.
  - Número total de tarefas.
  - Tarefas por status: `pendente`, `em andamento`, `concluída`.
- Suporte a filtros por intervalo de datas (`start_date_from` e `start_date_to`).

### **📩 1.4. Notificações**
- Envio de e-mails ao concluir uma tarefa.

### **🔑 1.5. Sistema de Roles e Permissões**
- **Admin:** Pode visualizar todos os projetos.
- **Usuário Comum:** Pode visualizar apenas seus próprios projetos.

---

## **2️⃣ Requisitos e Dependências**

### **📌 2.1. Requisitos**
- **PHP:** >= 8.3
- **Composer:** >= 2.0
- **Laravel:** 10.x
- **Banco de Dados:** PostgreSQL
- **Mailtrap (ou similar):** Para testes de envio de e-mails.
- **Docker e Docker Compose:** Para execução em containers.

### **📦 2.2. Dependências**
- **spatie/laravel-permission:** Gerenciamento de roles e permissões.
- **firebase/php-jwt:** Autenticação JWT.
- **guzzlehttp/guzzle:** Requisições HTTP (CEP, por exemplo).
- **phpunit/phpunit:** Testes automatizados.

---

## **3️⃣ Testes Automatizados**

### **🧪 3.1. Configuração do Ambiente de Testes**
Crie um arquivo `.env.testing` com a seguinte configuração para utilizar SQLite em memória:

```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

JWT_SECRET=Mg4ubszH659AUiKVDNv5Sk9JAM4VxKNS270OLxxidycKAawH2VWAORbYY7RocQYN
```

### **📌 3.2. Executar os Testes**
Para rodar todos os testes, utilize o comando:
```bash
php artisan test
```

---

## **4️⃣ Rotas da API**

### **🔐 4.1. Autenticação**
- **`POST /login`**: Login do usuário e retorno do token JWT.

### **📂 4.2. Gerenciamento de Projetos**
- **`GET /projects`**: Lista projetos.
  - **Admin:** Todos os projetos.
  - **Usuário comum:** Apenas seus próprios projetos.
- **`POST /projects`**: Cria um novo projeto.
- **`PUT /projects/{id}`**: Atualiza um projeto e suas tarefas.
- **`DELETE /projects/{id}`**: Exclui um projeto e suas tarefas associadas.

### **📊 4.3. Relatórios**
- **`GET /reports/projects`**: Gera um relatório com métricas de projetos e tarefas.

### **📍 4.4. Consulta de CEP**
- **`GET /cep/{cep}`**: Retorna informações de um CEP específico.

### **👤 4.5. Dados do Usuário**
- **`GET /me`**: Retorna os dados do usuário autenticado.

---

## **5️⃣ Decisões Técnicas**

1. **JWT para Autenticação:** Escolhido por ser leve e sem estado, ideal para APIs REST.
2. **Spatie Laravel Permission:** Facilita a implementação de roles e permissões.
3. **Notificações Laravel:** Utilizado para envio de e-mails ao usuário.
4. **Estrutura Modular:** Organização seguindo o padrão MVC do Laravel.
5. **Testes Automatizados:** Implementados para garantir a estabilidade da aplicação.

---

## **6️⃣ Como Rodar a Aplicação Completa (Frontend + Backend)**
### **📌 6.1. Clonar os Repositórios**
Clone os repositórios do frontend e backend:
```bash
git clone https://github.com/fael-silva/projectManagement.git backend
git clone https://github.com/fael-silva/project-management-front.git frontend
```
---

### **🔧 6.2. Ajustar Caminhos no Docker Compose**
O arquivo `docker-compose-project.yml` está localizado na raiz do backend, **mova-o para um diretório acima**, para que a estrutura fique assim:

```
/meu-projeto
│── docker-compose.yml
│── /backend # Código-fonte do backend
│   ├── .env  
│   ├── Dockerfile
│   ├── ...
│
│── /frontend # Código-fonte do frontend
│   ├── .env
│   ├── Dockerfile
│   ├── ...
```

Caso os repositórios sejam clonados em diretórios diferentes, edite o arquivo `docker-compose-project.yml` para ajustar os caminhos:

```yaml
  backend:
    build: ./backend
    env_file:
      - ./backend/.env

  frontend:
    build: ./frontend
    env_file:
      - ./frontend/.env
```
---

### **🚀 6.3. Subir os Containers**
Acesse o diretório do backend e execute o seguinte comando:
```bash
docker-compose -f docker-compose-project.yml up --build -d
```
---

### **🌐 6.4. Acesse a Aplicação**
Após iniciar os containers, a aplicação estará disponível nos seguintes endereços:

- **Frontend:** [http://localhost:3000](http://localhost:3000)
- **Backend:** [http://localhost:8000](http://localhost:8000)

#### **🔑 Credenciais de Acesso**
Após rodar as **migrations e seeders**, os seguintes usuários estarão disponíveis para login no sistema:

| **Usuário**  | **E-mail**            | **Senha**   | **Role (Permissão)** |
|-------------|----------------------|------------|---------------------|
| Admin       | `adm@example.com`     | `password` | Administrador (`admin`) |
| Usuário Padrão | `user@example.com`  | `password` | Usuário comum (`user`) |

O **usuário administrador** tem acesso total ao sistema, enquanto o **usuário comum** pode apenas visualizar seus próprios projetos.

Caso precise criar novos usuários, utilize o comando:
```bash
php artisan tinker
```
E crie novos registros manualmente.

---

### **📌 Observação Final**
Caso haja qualquer erro ao rodar a aplicação, verifique os logs dos containers com:
```bash
docker-compose logs -f
```
Se precisar reiniciar completamente, use:
```bash
docker-compose down -v && docker-compose up --build -d
```

---


