# **Documentação da API - Project Management**

API para gerenciamento de projetos e tarefas, com autenticação, notificações, permissões e relatórios.

---

## **1. Funcionalidades da API**

### **1.1. Autenticação**
- **Login com JWT:** Permite autenticação via token JWT.
- **Proteção de rotas:** Rotas protegidas por middleware `jwt.auth`.

### **1.2. Gerenciamento de Projetos**
- Criar, listar, atualizar e excluir projetos.
- Associar tarefas aos projetos no momento da criação ou atualização.
- Atualização automática de datas:
  - Quando o projeto é concluído, a `end_date` é atualizada.
  - Quando uma tarefa é concluída, o `completed_at` é atualizado.

### **1.3. Relatórios**
- Métricas gerais de projetos e tarefas:
  - Número total de projetos.
  - Projetos por status: `planejado`, `em andamento`, `concluído`.
  - Número total de tarefas.
  - Tarefas por status: `pendente`, `em andamento`, `concluída`.
- Filtro por intervalo de datas (`start_date_from` e `start_date_to`).

### **1.4. Notificações**
- Envio de e-mails ao concluir uma tarefa.

### **1.5. Sistema de Roles e Permissões**
- **Admin:**
  - Pode visualizar todos os projetos.
- **Usuário Comum:**
  - Só pode visualizar seus próprios projetos.

---

## **2. Requisitos e Dependências**

### **2.1. Requisitos**
- **PHP:** >= 8.3
- **Composer:** >= 2.0
- **Laravel:** 10.x
- **Banco de Dados:** PostgreSQL
- **Mailtrap (ou similar):** Para testes de envio de e-mails.
- **Docker e Docker Compose:** Para rodar em containers.

### **2.2. Dependências**
- **spatie/laravel-permission:** Gerenciamento de roles e permissões.
- **firebase/php-jwt:** Autenticação JWT.
- **guzzlehttp/guzzle:** Requisições HTTP (CEP, por exemplo).
- **phpunit/phpunit:** Testes automatizados.

---

## **3. Instruções para Rodar o Projeto**

### **3.1. Clonar o Repositório**
Clone o projeto no seu ambiente local:
```bash
git clone <url-do-repositorio>
cd project-management
```

### **3.2. Configurar o `.env`**
Copie o arquivo `.env.example` para `.env`:
```bash
cp .env.example .env
```

Configure as variáveis no `.env`, incluindo:

#### Banco de Dados:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=project_management
DB_USERNAME=postgres
DB_PASSWORD=secret
```

#### E-mails (Mailtrap para testes):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<seu_usuario>
MAIL_PASSWORD=<sua_senha>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seuprojeto.com
MAIL_FROM_NAME="Project Management"
```

### **3.3. Instalar Dependências**
Instale as dependências do projeto:
```bash
composer install
```

### **3.4. Gerar a Chave da Aplicação**
Gere a chave do Laravel:
```bash
php artisan key:generate
```

### **3.5. Migrar e Rodar Seeders**
Execute as migrações para criar as tabelas:
```bash
php artisan migrate
```

Execute os seeders para criar usuários e permissões:
```bash
php artisan db:seed
```

### **3.6. Rodar o Servidor**
Inicie o servidor local:
```bash
php artisan serve
```

O projeto estará acessível em: [http://localhost:8000](http://localhost:8000).

---

## **4. Como Rodar com Docker**

### **4.1. Requisitos**
Certifique-se de que o Docker e o Docker Compose estão instalados.

### **4.2. Subir os Containers**
1. Crie os arquivos `Dockerfile` e `docker-compose.yml` (já fornecidos).
2. Suba os containers:
   ```bash
   docker-compose up -d
   ```

### **4.3. Configurar o Backend**
1. Acesse o container do Laravel:
   ```bash
   docker exec -it project_backend bash
   ```
2. Execute as migrações e seeders:
   ```bash
   php artisan migrate --seed
   ```

---

## **5. Testes Automatizados**

### **5.1. Configuração do Ambiente de Testes**
Configure o arquivo `.env.testing` para usar SQLite em memória:
```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

JWT_SECRET=Mg4ubszH659AUiKVDNv5Sk9JAM4VxKNS270OLxxidycKAawH2VWAORbYY7RocQYN
```

### **5.2. Executar os Testes**
Rode todos os testes:
```bash
php artisan test
```

---

## **6. Rotas da API**

### **6.1. Autenticação**
- **`POST /login`**: Login do usuário e retorno do token JWT.

### **6.2. Gerenciamento de Projetos**
- **`GET /projects`**: Lista projetos.
  - Admin: Todos os projetos.
  - Usuário comum: Apenas seus próprios projetos.
- **`POST /projects`**: Cria um novo projeto.
- **`PUT /projects/{id}`**: Atualiza um projeto e suas tarefas.
- **`DELETE /projects/{id}`**: Exclui um projeto e suas tarefas associadas.

### **6.3. Relatórios**
- **`GET /reports/projects`**: Gera um relatório com métricas de projetos e tarefas.
  - Filtros: aplicado em cima da data de criação de um projeto
    - `start_date_from`
    - `start_date_to`

### **6.4. CEP**
- **`GET /cep/{cep}`**: Retorna as informações de um CEP específico.

### **6.5. Dados do Usuário**
- **`GET /me`**: Retorna os dados do usuário autenticado.


---

## **7. Decisões Técnicas**

1. **JWT para Autenticação:**
   - Optei por JWT para autenticação leve e sem estado, ideal para APIs REST.

2. **Spatie Laravel Permission:**
   - Escolhi este pacote para facilitar a implementação de roles e permissões.

3. **Notificações Laravel:**
   - O sistema de notificações nativo do Laravel foi usado para enviar e-mails.

4. **Estrutura Modular:**
   - O projeto segue o padrão MVC do Laravel.

5. **Testes Automatizados:**
   - Implementei testes unitários e de integração para garantir a estabilidade.

---

## **8. Como Rodar a Aplicação Completa (Frontend + Backend)**

### **8.1. Clone os Repositórios**
Clone os repositórios do frontend e backend:
```bash
git clone <url-backend>
git clone <url-frontend>
```

### **8.2. Configurar o Backend**
1. Acesse o diretório do backend:
   ```bash
   cd backend
   ```
2. Suba os containers:
   ```bash
   docker-compose up -d
   ```
3. Configure o Laravel dentro do container:
   ```bash
   docker exec -it project_backend bash
   composer install
   php artisan migrate --seed
   ```

### **8.3. Configurar o Frontend**
1. Acesse o diretório do frontend:
   ```bash
   cd frontend
   ```
2. Suba o container do frontend:
   ```bash
   docker-compose up -d
   ```

### **8.4. Acesse a Aplicação**
- Frontend: [http://localhost:3000](http://localhost:3000)
- Backend: [http://localhost:8000](http://localhost:8000)
