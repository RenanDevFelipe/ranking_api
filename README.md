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
    "email": "joao@email.com"
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
    "cargo": "Técnico"
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
    "data": "2024-03-28"
  }
]
```

### 6. Consultar Ranking Diario por Técnico

```http
POST /Ranking/RankingDiario
```

**Corpo da Requisição:**

```json
{
    "id": 19,
    "data_request": "2025-04-02"
}
```

**Resposta:**

```json
[
{
    "media_setor": [
        {
            "id_setor": 8,
            "total_registros": 3,
            "media_diaria": "3.33",
            "soma_pontuacao": "10.00"
        },
        {
            "id_setor": 9,
            "total_registros": 1,
            "media_diaria": "10.00",
            "soma_pontuacao": "40.00"
        },
        {
            "id_setor": 5,
            "tota_registros": 3,
            "media_diaria": "9.61",
            "soma_pontacao": "28.82"
        },
        {
            "id_setor": 6,
            "total_registros": 1,
            "media_diaria": "10.00",
            "soma_pontuacao": "10.00"
        },
        {
            "id_setor": 7,
            "total_registros": 1,
            "media_diaria": "10.00",
            "soma_pontuacao": "30.00"
        }
    ],
    "media_total": "8.59"
}
]
```

## Endpoints da API IXCSoft

### 7. Listar O.S Finalizadas do Técnico

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
        "id": "539627",
        "id_cliente": "4731",
        "id_assunto": "28",
        "cliente": "ROMILDO DE ARRUDA SILVA",
        "finalizacao": "2025-03-28 09:59:28",
        "mensagem": "Cliente  se acesso chegando era seu cabo luga errado  foi colcoado luga certo  feito os teste com cliente ficou tudo ok",
        "checklist": "Avaliação da OS ID: 539627\r\n\r\nA ordem de serviço estava com o Status em \"Execução\"?\r\nSim (X) Não ( )\r\n\r\nFoi aferida a potência do Sinal, na casa do cliente e na CTO? Frequência 1490nm.\r\nSim (X) Não ( )\r\n\r\nA potência do sinal óptico ficou na margem de sinal permitido = ou < que -25db?\r\nSim (X) Não ( )\r\n\r\nOs Equipamentos e cabos ficaram organizados na parede, de acordo com o Padrão Ti Connect?\r\nSim ( ) Não (X)\r\n\r\nFoi Feito o teste de velocidade?\r\nSim (X) Não ( )\r\n\r\nO teste de velocidade bateu com o plano do cliente?\r\nSim (X) Não ( )\r\n\r\nFoi ativado o Ping e liberado o acesso remoto?\r\nSim (X) Não ( )\r\n\r\nOBS: Nenhuma observação\r\n"
    },
    {
        "id": "539603",
        "id_cliente": "10223",
        "id_assunto": "28",
        "cliente": "IVSON GOMES DE LIMA",
        "finalizacao": "2025-03-28 08:57:22",
        "mensagem": "Cliente  se acesso  chegando  aki foi visto conector  do cliente  tava fora da cto foi colocado e colocado nome tbm feito os teste com cliente ficou tudo",
        "checklist": "Avaliação da OS ID: 539603\r\n\r\nA ordem de serviço estava com o Status em \"Execução\"?\r\nSim (X) Não ( )\r\n\r\nFoi aferida a potência do Sinal, na casa do cliente e na CTO? Frequência 1490nm.\r\nSim (X) Não ( )\r\n\r\nA potência do sinal óptico ficou na margem de sinal permitido = ou < que -25db?\r\nSim (X) Não ( )\r\n\r\nOs Equipamentos e cabos ficaram organizados na parede, de acordo com o Padrão Ti Connect?\r\nSim ( ) Não (X)\r\n\r\nFoi Feito o teste de velocidade?\r\nSim (X) Não ( )\r\n\r\nO teste de velocidade bateu com o plano do cliente?\r\nSim ( ) Não (X)\r\n\r\nFoi ativado o Ping e liberado o acesso remoto?\r\nSim (X) Não ( )\r\n\r\nOBS: Nenhuma observação\r\n"
    },
    {
        "id": "539509",
        "id_cliente": "11404",
        "id_assunto": "70",
        "cliente": "EVELLY KARINA DE ANDRADE BEZERRA",
        "finalizacao": "2025-03-28 13:51:42",
        "mensagem": "Mudança de endereço  foi gasto 2 conector verde e azul  uma caixa de acomodação 10 fixa fio feito os teste com cliente ficou tudo ok",
        "checklist": "Avaliação da OS ID: 539509\r\n\r\nA ordem de serviço estava com o Status em \"Execução\"?\r\nSim (X) Não ( )\r\n\r\nFoi aferida a potência do Sinal, na casa do cliente e na CTO? Frequência 1490nm.\r\nSim (X) Não ( )\r\n\r\nA potência do sinal óptico ficou na margem de sinal permitido = ou < que -25db?\r\nSim (X) Não ( )\r\n\r\nConfigurações do equipamento Canal e Largura\r\nSim (X) Não ( )\r\n\r\nFoi Feito o teste de velocidade?\r\nSim (X) Não ( )\r\n\r\nO teste de velocidade bateu com o plano do cliente?\r\nSim (X) Não ( )\r\n\r\nFoi ativado o Ping e liberado o acesso remoto?\r\nSim (X) Não ( )\r\n\r\nOs Equipamentos e cabos ficaram organizados na parede, de acordo com o Padrão Ti Connect?\r\nSim (X) Não ( )\r\n\r\nO tecnico mandou a localização/id e porta da CTO?\r\nSim (X) Não ( )\r\n\r\nOBS: Nenhuma observação\r\n"
    },
    {
        "id": "538123",
        "id_cliente": "13265",
        "id_assunto": "79",
        "cliente": "SEVERINO ALEXANDRE DA SILVA",
        "finalizacao": "2025-03-28 15:17:30",
        "mensagem": "Retirada ok",
        "checklist": "Não preenchido"
    }
]
```

### 8. Consultar Cliente

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

### 9. Consultar Departamento

```http
GET /Departamento/getAll
```

**Resposta:**

```json
{
    "total": 9,
    "registros": [
        {
            "id_setor": 5,
            "nome_setor": "Suporte Nível 3"
        },
        {
            "id_setor": 6,
            "nome_setor": "Estoque"
        },
        {
            "id_setor": 7,
            "nome_setor": "Recursos Humanos"
        },
        {
            "id_setor": 8,
            "nome_setor": "Sucesso ao cliente"
        },
        {
            "id_setor": 9,
            "nome_setor": "Avaliadores suporte Nível 2"
        },
        {
            "id_setor": 21,
            "nome_setor": "Atendimento"
        },
        {
            "id_setor": 22,
            "nome_setor": "Suporte Nivel 2"
        },
        {
            "id_setor": 23,
            "nome_setor": "Infraestrutura"
        },
        {
            "id_setor": 24,
            "nome_setor": "Câmeras"
        }
    ]
}
```

### 10. Consultar Media Mensal

```http
POST /Ranking/MediaMensal
```

**Corpo da Requisição:**

```json
{
    "id_tecnico": 20,
    "data_request": "2025-04"
}
```

**Resposta:**

```json
{
    "tecnico": "Jadson Daniel",
    "total_registros": 38,
    "media_mensal": "9.91",
    "media_setor": [
        {
            "id_setor": 5,
            "setor": "Suporte Nível 3",
            "total_registros": 8,
            "media_mensal": "9.88",
            "soma_pontuacao": "79.00"
        },
        {
            "id_setor": 8,
            "setor": "Sucesso ao cliente",
            "total_registros": 6,
            "media_mensal": "10.00",
            "soma_pontuacao": "60.00"
        },
        {
            "id_setor": 9,
            "setor": "Avaliadores suporte Nível 2",
            "total_registros": 8,
            "media_mensal": "9.69",
            "soma_pontuacao": "310.00"
        },
        {
            "id_setor": 7,
            "setor": "Recursos Humanos",
            "total_registros": 8,
            "media_mensal": "10.00",
            "soma_pontuacao": "240.00"
        },
        {
            "id_setor": 6,
            "setor": "Estoque",
            "total_registros": 8,
            "media_mensal": "10.00",
            "soma_pontuacao": "80.00"
        }
    ]
}
```

### 11. Consultar Ranking Mensal

```http
POST /Ranking/GetRankingMensal
```

**Corpo da Requisição:**

```json
{
    "data_request": "2025-04"
}
```

**Resposta:**

```json

[
    {
        "tecnico": "Madson Araújo",
        "colocacao": 1,
        "total_registros": 35,
        "media_mensal": "9.93",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 7,
                "media_mensal": "9.78",
                "soma_pontuacao": "68.49"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 4,
                "media_mensal": "10.00",
                "soma_pontuacao": "40.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "320.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "9.88",
                "soma_pontuacao": "79.00"
            }
        ]
    },
    {
        "tecnico": "Jadson Daniel",
        "colocacao": 2,
        "total_registros": 38,
        "media_mensal": "9.91",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 8,
                "media_mensal": "9.88",
                "soma_pontuacao": "79.00"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 6,
                "media_mensal": "10.00",
                "soma_pontuacao": "60.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "9.69",
                "soma_pontuacao": "310.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "80.00"
            }
        ]
    },
    {
        "tecnico": "João Paulo",
        "colocacao": 3,
        "total_registros": 27,
        "media_mensal": "9.73",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 1,
                "media_mensal": "8.67",
                "soma_pontuacao": "8.67"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 2,
                "media_mensal": "10.00",
                "soma_pontuacao": "20.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "320.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "80.00"
            }
        ]
    },
    {
        "tecnico": "Luis Eduardo",
        "colocacao": 4,
        "total_registros": 33,
        "media_mensal": "9.30",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 5,
                "media_mensal": "7.99",
                "soma_pontuacao": "39.95"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 4,
                "media_mensal": "8.75",
                "soma_pontuacao": "35.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "320.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "9.75",
                "soma_pontuacao": "78.00"
            }
        ]
    },
    {
        "tecnico": "Marcos Venícios",
        "colocacao": 5,
        "total_registros": 49,
        "media_mensal": "9.03",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 13,
                "media_mensal": "8.78",
                "soma_pontuacao": "114.14"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 12,
                "media_mensal": "6.67",
                "soma_pontuacao": "80.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "9.69",
                "soma_pontuacao": "310.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "80.00"
            }
        ]
    },
    {
        "tecnico": "Paulo Ricardo",
        "colocacao": 6,
        "total_registros": 44,
        "media_mensal": "8.80",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 13,
                "media_mensal": "8.90",
                "soma_pontuacao": "115.71"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 7,
                "media_mensal": "5.71",
                "soma_pontuacao": "40.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "9.38",
                "soma_pontuacao": "300.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "80.00"
            }
        ]
    },
    {
        "tecnico": "Robson Augusto",
        "colocacao": 7,
        "total_registros": 46,
        "media_mensal": "8.39",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 14,
                "media_mensal": "8.85",
                "soma_pontuacao": "123.83"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 8,
                "media_mensal": "3.13",
                "soma_pontuacao": "25.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "320.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "80.00"
            }
        ]
    },
    {
        "tecnico": "Rubens Matheus",
        "colocacao": 8,
        "total_registros": 40,
        "media_mensal": "8.08",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 11,
                "media_mensal": "8.42",
                "soma_pontuacao": "92.61"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 5,
                "media_mensal": "2.00",
                "soma_pontuacao": "10.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "320.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "80.00"
            }
        ]
    },
    {
        "tecnico": "Gleyson Carlos",
        "colocacao": 9,
        "total_registros": 49,
        "media_mensal": "7.69",
        "media_setor": [
            {
                "id_setor": 5,
                "setor": "Suporte Nível 3",
                "total_registros": 15,
                "media_mensal": "7.79",
                "soma_pontuacao": "116.78"
            },
            {
                "id_setor": 8,
                "setor": "Sucesso ao cliente",
                "total_registros": 10,
                "media_mensal": "1.00",
                "soma_pontuacao": "10.00"
            },
            {
                "id_setor": 9,
                "setor": "Avaliadores suporte Nível 2",
                "total_registros": 8,
                "media_mensal": "9.69",
                "soma_pontuacao": "310.00"
            },
            {
                "id_setor": 7,
                "setor": "Recursos Humanos",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "240.00"
            },
            {
                "id_setor": 6,
                "setor": "Estoque",
                "total_registros": 8,
                "media_mensal": "10.00",
                "soma_pontuacao": "80.00"
            }
        ]
    }
]

```

### 12. Consultar Tutoriais

```http
GET /Tutorial/getAll
```

**Resposta:**

```json

{
    "total": 1,
    "registros": [
        {
            "id": 1,
            "title": "provisionar ONU/ONT",
            "descricao": "como provisionar uma ONU/ONT",
            "url_view": "https://drive.google.com/file/d/1wnBplehYL_96XrRoWN617HFbL5y4nTZe/view?usp=drivesdk",
            "url_download": "https://drive.usercontent.google.com/u/0/uc?id=1wnBplehYL_96XrRoWN617HFbL5y4nTZe&export=download",
            "criado_por": "Ellyson",
            "data_criacao": "2025-04-08",
            "name_icon": "picture_as_pdf"
        }
    ]
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

