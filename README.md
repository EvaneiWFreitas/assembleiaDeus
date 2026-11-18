# ⛪ AssembleiaDeus – Sistema de Gestão de Igrejas

**Sistema Web para Administração, Organização e Gestão de Igrejas, Congregações e Ministérios.**
---

## 📖 Sobre o Projeto

O **AssembleiaDeus** é um sistema web desenvolvido com o objetivo de facilitar a administração e o gerenciamento das atividades de uma instituição religiosa.

A plataforma busca centralizar informações administrativas, ministeriais e organizacionais, proporcionando maior controle, segurança e praticidade para pastores, administradores, secretários e responsáveis pelas congregações.

O sistema foi idealizado para atender às necessidades de igrejas, ministérios, congregações e pontos de pregação, permitindo uma gestão organizada e eficiente.

## 🎯 Objetivos do Sistema

- Modernizar a administração das igrejas.
- Centralizar os cadastros e informações institucionais.
- Facilitar o gerenciamento de membros e obreiros.
- Organizar igrejas matrizes, congregações e pontos de pregação.
- Melhorar o controle administrativo e ministerial.
- Facilitar o acesso às informações por meio de uma interface intuitiva.

## 🚀 Funcionalidades

### 👥 Gestão de Membros
- Cadastro de membros.
- Atualização de informações pessoais.
- Consulta e pesquisa de membros.
- Organização dos registros cadastrais.

### 🙏 Gestão de Obreiros
- Cadastro de obreiros.
- Controle de informações ministeriais.
- Organização por cargos e funções.
- Consulta de registros.

### ⛪ Gestão de Igrejas
- Cadastro de igreja matriz.
- Cadastro de congregações.
- Cadastro de pontos de pregação.
- Organização hierárquica das unidades.
- Identificação dos responsáveis.

### 📊 Painel Administrativo
- Dashboard com indicadores gerais.
- Resumo dos cadastros.
- Acesso rápido aos módulos.
- Organização das informações administrativas.

### 🔐 Segurança e Controle de Acesso
- Sistema de autenticação.
- Controle de acesso administrativo.
- Proteção das páginas internas.
- Validação dos dados enviados pelos formulários.

## 🛠️ Tecnologias Utilizadas

| Tecnologia | Finalidade |
|---|---|
| PHP | Desenvolvimento do backend |
| MySQL | Gerenciamento do banco de dados |
| Bootstrap | Interface responsiva |
| HTML5 | Estrutura das páginas |
| CSS3 | Estilização |
| JavaScript | Interatividade |
| PDO | Comunicação com o banco de dados |
| MVC | Organização da arquitetura |

## 🏗️ Arquitetura do Projeto

O sistema utiliza o padrão arquitetural **MVC (Model-View-Controller)**, separando as responsabilidades da aplicação.

- **Model:** responsável pelas regras de negócio e comunicação com o banco de dados.
- **View:** responsável pela apresentação visual e interface do usuário.
- **Controller:** responsável por receber as requisições, processar informações e controlar o fluxo da aplicação.

Estrutura sugerida:

```text
assembleiaDeus/
│
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── membros/
│   │   ├── obreiros/
│   │   ├── igrejas/
│   │   ├── congregacoes/
│   │   └── pontos_pregacao/
│   │
│   └── Core/
│
├── config/
│   ├── database.php
│   └── app.php
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── img/
│   │   └── uploads/
│   │
│   └── index.php
│
├── routes/
│   └── web.php
│
├── database/
│   └── gestao_igreja.sql
│
├── .htaccess
├── .env.example
└── README.md
```

*Observação: a estrutura acima representa a organização proposta para o projeto e deve ser ajustada conforme os diretórios efetivamente existentes.*

## 💻 Requisitos para Instalação

- PHP 8.0 ou superior (conforme compatibilidade do projeto).
- MySQL 5.7 ou superior.
- Apache.
- XAMPP ou ambiente equivalente.
- Navegador atualizado.

## ⚙️ Como Instalar e Executar

### 1. Clonar ou copiar o projeto

Coloque a pasta do projeto no diretório:

```text
C:\xampp\htdocs\assembleiaDeus
```

### 2. Iniciar o servidor

Abra o XAMPP e inicie:

- Apache.
- MySQL.

### 3. Criar o banco de dados

Acesse o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Crie o banco de dados:

```sql
CREATE DATABASE gestao_igreja
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Importe o arquivo SQL disponibilizado na pasta `database`.

### 4. Configurar a conexão

Configure as credenciais de acesso ao MySQL no arquivo de configuração do projeto.

Exemplo:

```php
$host = 'localhost';
$dbname = 'gestao_igreja';
$username = 'root';
$password = '';
```

### 5. Executar o sistema

Abra o navegador e acesse:

```text
http://localhost/assembleiaDeus/public/
```

Ajuste o endereço conforme a configuração do servidor e o ponto de entrada da aplicação.

## 🔒 Segurança

O projeto deve seguir boas práticas de desenvolvimento:

- Utilização de consultas preparadas com PDO.
- Proteção contra SQL Injection.
- Validação e sanitização de dados.
- Controle de sessões.
- Senhas armazenadas com `password_hash()`.
- Verificação de permissões dos usuários.
- Proteção de arquivos de configuração.
- Implementação de proteção CSRF.

## 🌐 Hospedagem

O sistema poderá ser preparado para implantação em servidores com suporte a PHP e MySQL.

Antes da publicação, configure:

- Credenciais do banco de dados.
- Versão compatível do PHP.
- Permissões dos diretórios.
- Arquivo `.htaccess`.
- Configurações de segurança.
- Ambiente de produção.

## 📌 Melhorias Futuras

- Gestão financeira da igreja.
- Controle de dízimos e ofertas.
- Emissão de relatórios em PDF.
- Gestão de eventos e cultos.
- Controle de departamentos.
- Gestão de aniversariantes.
- Cadastro de visitantes.
- Controle de transferências de membros.
- Emissão de carteirinhas.
- Sistema de comunicação com membros.
- Relatórios administrativos e ministeriais.

## 👨‍💻 Desenvolvedor

**Desenvolvido por: Dev. Evanei Freitas**

Projeto voltado à modernização da gestão administrativa e organizacional de instituições religiosas.

## 📜 Licença

Projeto de uso institucional. Os termos de distribuição e utilização deverão ser definidos pelo responsável pelo desenvolvimento.

---

**AssembleiaDeus – Tecnologia a serviço da Igreja, organização a serviço do Reino de Deus!**

*“Tudo, porém, seja feito com decência e ordem.” — 1 Coríntios 14:40.*
