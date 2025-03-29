# Ranking API

## Descrição

A **Ranking API** é uma API desenvolvida em PHP para gerenciar rankings de desempenho e interagir com o sistema IXCSoft. Ela permite listar usuários, realizar login/logout, consultar rankings e obter informações de clientes e ordens de serviço do IXCSoft.

## Tecnologias Utilizadas

- **PHP** (Backend)
- **MySQL** (Banco de Dados)
- **Apache/Nginx** (Servidor Web)
- **Postman** (Para testes de API)

## Instalação

### 1. Clonar o repositório

```sh
  git clone https://github.com/RenanDevFelipe/ranking_api.git
```

### 2. Configurar o banco de dados

- Crie um banco de dados MySQL.
- Importe o arquivo `database.sql` (caso esteja disponível no repositório).
- Configure a conexão com o banco em `core/core.php`:
  ```php
  define('DB_HOST', 'seu_servidor');
  define('DB_NAME', 'seu_banco');
  define('DB_USER', 'seu_usuario');
  define('DB_PASS', 'sua_senha');
  ```

### 3. Iniciar o servidor

Caso esteja usando o PHP embutido:

```sh
php -S localhost:8000 -t public
```

Se estiver utilizando Apache/Nginx, configure um virtual host apontando para a pasta correta.

## Endpoints da API

### 1. Listar Todos os Usuários

```http
GET /User/listAll
```

**Resposta:**

```json
[
  {
    "id": 1,
    "nome": "João Silva",
    "email": "joao@email.com"(...)
  }
]
```

### 2. Login de Usuário

```http
POST /Account/login
```

**Corpo da Requisição:**

```json
{
  "email": "usuario@email.com",
  "password": "senha123"
}
```

**Resposta:**

```json
{
  "access_token": "JWT_TOKEN",
  "nome": "SEU_NOME",
  "email": "email@exemple.com",
  "id_ixc": 190
  
}
```

### 3. Logout

```http
POST /Account/logout
```

**Resposta:**

```json
{
  "mensagem": "Logout realizado com sucesso"
}
```

### 4. Listar Todos os Colaboradores

```http
GET /Colaborador/GetAll
```

**Resposta:**

```json
[
  {
    "id": 1,
    "nome": "Carlos Mendes",
    "cargo": "Técnico"...
  }
]
```

### 5. Consultar Ranking Diário Geral

```http
POST /Ranking/RankingDiarioGeral
```

**Corpo da Requisição:**

```json
{
  "data_request": "2024-03-28"
}
```

**Resposta:**

```json
[
  {
    "usuario": "Ana Souza",
    "pontuacao": 8.8,
    "data": "2024-03-28"...
  }
]
```

## Endpoints da API IXCSoft

### 6. Listar O.S Finalizadas do Técnico

```http
POST /IXCSoft/listOSFinTec
```

**Corpo da Requisição:**

```json
{
  "query": "nome_do_tecnico",
  "data_fechamento": "2024-03-28"
}
```

**Resposta:**

```json
[
  {
    "id_os": 12345,
    "tecnico": "Carlos Mendes",
    "status": "F"...
  }
]
```

### 7. Consultar Cliente

```http
POST /IXCSoft/Cliente
```

**Corpo da Requisição:**

```json
{
  "query": "id_cliente"
}
```

**Resposta:**

```json
{
  "id_cliente": 6789,
  "nome": "João Souza",
  "telefone": "(11) 99999-9999"
}
```

## Tratamento de Erros

Caso uma rota inexistente seja acessada, a API retorna:

```json
{
  "erro": "Rota inexistente ou Requisição inválida"
}
```

## Contribuição

Contribuições são bem-vindas! Para contribuir:

1. Fork o repositório.
2. Crie uma branch (`feature-minha-mudanca`).
3. Commit suas mudanças.
4. Envie um Pull Request.

## Licença

Este projeto está sob a licença MIT - consulte o arquivo [LICENSE](LICENSE) para mais detalhes.

