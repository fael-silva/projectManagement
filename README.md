
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
- **PHP:** >= 8.1
- **Composer:** >= 2.0
- **Laravel:** 10.x
- **Banco de Dados:** PostgreSQL (ou SQLite para testes)
- **Mailtrap (ou similar):** Para testes de envio de e-mails.

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

Configure as seguintes variáveis no `.env`:

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

1. Certifique-se de que o Docker e o Docker Compose estão instalados.
2. Crie os arquivos `Dockerfile` e `docker-compose.yml` (já fornecidos anteriormente).
3. Suba os containers:
   ```bash
   docker-compose up -d
   ```
4. Acesse o container do Laravel:
   ```bash
   docker exec -it laravel-app bash
   ```
5. Execute as migrações e seeders:
   ```bash
   php artisan migrate --seed
   ```

---

## **5. Testes Automatizados**

### **5.1. Configuração do Ambiente de Testes**
Configure o arquivo `.env.testing` para usar SQLite em memória:
```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
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
  - Filtros:
    - `start_date_from`
    - `start_date_to`

---

## **7. Decisões Técnicas**

1. **JWT para Autenticação:**
   - Optamos por JWT para autenticação leve e sem estado, ideal para APIs REST.

2. **Spatie Laravel Permission:**
   - Escolhemos este pacote para facilitar a implementação de roles e permissões, com boa escalabilidade e integração com o Laravel.

3. **Notificações Laravel:**
   - O sistema de notificações nativo do Laravel foi usado para enviar e-mails, com suporte a filas para melhorar a performance.

4. **Estrutura Modular:**
   - O projeto segue o padrão MVC do Laravel, com controladores para endpoints, modelos para interações com o banco e notificações para envio de e-mails.

5. **Testes Automatizados:**
   - Implementamos testes unitários e de integração para garantir a estabilidade das principais funcionalidades.

---
