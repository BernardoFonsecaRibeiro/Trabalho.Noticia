# Regionais RS - Portal de Notícias

> Sistema web completo para gestão e publicação de notícias do Rio Grande do Sul, desenvolvido em PHP com banco de dados MySQL, utilizando o XAMPP como ambiente de desenvolvimento.

## 📌 Sobre o Projeto

Este repositório contém um **Portal de Notícias** focado no Rio Grande do Sul, com um sistema completo que inclui:

- **Sistema de Login e Autenticação** com três níveis de acesso (Leitor, Repórter e Administrador)
- **Cadastro de Usuários** - Leitores, Repórteres e Administradores
- **Gestão de Notícias** - Cadastro, edição, visualização e exclusão de notícias
- **Upload de Imagens** para ilustrar as notícias
- **Painéis Personalizados** para cada tipo de usuário
- **Programação Orientada a Objetos** com uso de POO e Banco de dados
- **CRUD Completo** de notícias e usuários

O projeto visa consolidar o aprendizado em desenvolvimento web e pode ser utilizado como portfólio profissional.

---

## 🛠️ Tecnologias Utilizadas

- **PHP** (8.2+ recomendado)
- **MySQL/MariaDB** - Banco de dados relacional
- **HTML5 & CSS3** - Estruturação e estilização
- **JavaScript** - Validações e interatividade
- **XAMPP** - Ambiente de desenvolvimento local (Apache + MySQL + PHP)
- **Git/GitHub** para versionamento

---

## 🚀 Como Executar

### Pré-requisitos

- [XAMPP](https://www.apachefriends.org/) instalado (ou similar com Apache + MySQL)
- Navegador web moderno

### Passo a Passo

1. **Instale e inicie o XAMPP**:
   - Inicie os serviços **Apache** e **MySQL** no painel do XAMPP

2. **Clone o repositório**:
   ```bash
   git clone https://github.com/seu-usuario/Regionais-RS.git
   ```

3. **Mova o projeto para a pasta do XAMPP**:
   ```bash
   # Copie a pasta para o diretório htdocs do XAMPP
   # Exemplo: C:\xampp\htdocs\Noticia
   ```

4. **Importe o banco de dados**:
   - Acesse o phpMyAdmin: `http://localhost/phpmyadmin`
   - Crie um banco de dados chamado `sistema_cadastro`
   - Importe o arquivo `sistema_cadastro.sql`
   - Importe também o arquivo `noticias.sql` para a tabela de notícias

5. **Acesse a aplicação**:
   ```
   http://localhost/Noticia/
   ```

6. **Configure a conexão** (se necessário):
   - Edite o arquivo `conexao.php` se suas credenciais do MySQL forem diferentes do padrão do XAMPP

---

## 📂 Estrutura do Repositório

```bash
📂 Noticia
├── 📁 img/                          # Imagens estáticas do site
├── 📁 imagens_noticias/             # Imagens upload das notícias
│
├── 📄 index.php                     # Página inicial pública
├── 📄 login.php                     # Sistema de login
├── 📄 logout.php                    # Encerramento de sessão
│
├── 📄 cadPublico.php                # Cadastro de leitor
├── 📄 cadReporter.php               # Cadastro de repórter
├── 📄 cadAdmin.php                  # Cadastro de administrador
├── 📄 cadNoticias.php               # Cadastro de notícias
│
├── 📄 indexPublico.php              # Painel do leitor
├── 📄 indexReporter.php             # Painel do repórter
├── 📄 indexAdmin.php                # Painel do administrador
│
├── 📄 noticias_publicas.php         # Listagem pública de notícias
├── 📄 noticia.php                   # Visualização individual de notícia
│
├── 📄 editar_noticia.php            # Edição de notícias
├── 📄 editar_perfil.php             # Edição de perfil
├── 📄 editar_usuario.php            # Edição de usuários (admin)
│
├── 📄 excluir_noticia.php           # Exclusão de notícias
├── 📄 excluir_perfil.php            # Exclusão de perfil
├── 📄 excluir_usuario.php           # Exclusão de usuários (admin)
│
├── 📄 admin_auth.php                # Autenticação de admin
├── 📄 autenticar.php                # Validação de autenticação
├── 📄 usuarios.php                  # Gestão de usuários
├── 📄 usuario.php                   # Dados do usuário
│
├── 📄 conexao.php                   # Conexão com banco de dados
├── 📄 index.css                     # Estilos da página inicial
├── 📄 cad.css                       # Estilos dos cadastros
│
├── 📄 sistema_cadastro.sql          # Script do banco (usuários)
├── 📄 noticias.sql                  # Script do banco (notícias)
└── 📄 README.md                     # Documentação do projeto
```

---

## 📖 Exemplos de Código

### Conexão com Banco de Dados (`conexao.php`)

```php
<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "sistema_cadastro";

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
```

### Sistema de Login com Níveis de Acesso (`login.php`)

```php
<?php
session_start();
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo_acesso = $_POST['tipo_acesso']; // 'publico', 'reporter' ou 'admin'

    // Define a tabela conforme o tipo selecionado
    switch($tipo_acesso) {
        case 'admin':
            $tabela = 'admins';
            $pagina_destino = 'indexAdmin.php';
            break;
        case 'reporter':
            $tabela = 'reporters';
            $pagina_destino = 'indexReporter.php';
            break;
        default:
            $tabela = 'usuarios';
            $pagina_destino = 'indexPublico.php';
    }

    // Busca usuário na tabela correta
    $stmt = $conn->prepare("SELECT id, nome, email, senha FROM $tabela WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();

        // Verifica a senha criptografada
        if (password_verify($senha, $usuario['senha'])) {
            // ✅ LOGIN SUCESSO - Cria a sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['tipo'] = $tipo_acesso;

            header("Location: $pagina_destino");
            exit();
        }
    }
}
?>
```

### Cadastro de Notícias com Upload de Imagem (`cadNoticias.php`)

```php
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $noticia = trim($_POST['noticia']);
    $autor = $_SESSION['usuario_id'];
    $tipo_autor = $_SESSION['tipo'];
    $data = !empty($_POST['data']) ? $_POST['data'] : null;

    // Upload da imagem
    $imagem = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $pasta_destino = "imagens_noticias/";
        
        if (!file_exists($pasta_destino)) {
            mkdir($pasta_destino, 0777, true);
        }

        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $nome_imagem = uniqid() . "_" . time() . "." . $extensao;
        $caminho_completo = $pasta_destino . $nome_imagem;

        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($extensao, $extensoes_permitidas)) {
            if ($_FILES['imagem']['size'] <= 5242880) { // 5MB max
                move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo);
                $imagem = $caminho_completo;
            }
        }
    }

    // Insere no banco de dados
    $stmt = $conn->prepare("INSERT INTO noticias (titulo, noticia, data, autor, tipo_autor, imagem) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $titulo, $noticia, $data, $autor, $tipo_autor, $imagem);
    $stmt->execute();
}
?>
```

### Estrutura do Banco de Dados

**Tabela de Usuários (usuarios):**
```sql
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `senha` varchar(255) NOT NULL,
  `data_cadastro` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

**Tabela de Notícias (noticias):**
```sql
CREATE TABLE `noticias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `noticia` text NOT NULL,
  `data` datetime(6) NOT NULL,
  `autor` int(100) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);
```

---

## 👥 Perfis de Usuário

O sistema possui **3 tipos de usuários**:

### 👤 Leitor (Público)
- Visualizar notícias publicadas
- Criar conta como leitor
- Editar/excluir seu próprio perfil

### ✍️ Repórter
- Todas as funcionalidades de Leitor
- Cadastrar novas notícias
- Editar/excluir suas próprias notícias
- Upload de imagens para ilustrar notícias

### 👑 Administrador
- Gerenciar todos os usuários
- Editar/excluir qualquer notícia
- Gerenciar conteúdo do site
- Acesso completo ao sistema

---

## 🔐 Segurança

- ✅ **Senhas criptografadas** usando `password_hash()` do PHP
- ✅ **Prepared Statements** para prevenir SQL Injection
- ✅ **Sessões** para controle de autenticação
- ✅ **Validação de upload** com limite de tamanho e extensões permitidas
- ✅ **Verificação de permissões** em páginas restritas

---

## 🎯 Funcionalidades

- ✅ CRUD completo de notícias
- ✅ CRUD completo de usuários
- ✅ Sistema de login com 3 níveis de acesso
- ✅ Upload de imagens com validação
- ✅ Painéis personalizados por tipo de usuário
- ✅ Edição e exclusão de perfil
- ✅ Visualização pública de notícias
- ✅ Interface responsiva e moderna
- ✅ Página institucional sobre o Rio Grande do Sul

---

## 🏆 Autor(es)

👤 **Bernardo Ribeiro**  
📧 Email: lobilho1976u8@gmail.com  
🔗 [GitHub](https://github.com/BernardoFonsecaRibeiro/)

---

## 🎯 Objetivo do Repositório

Este repositório serve como um **portfólio** para demonstrar habilidades em:

- **Desenvolvimento Web** com PHP e MySQL
- **Programação Orientada a Objetos** (POO)
- **Banco de Dados Relacional** (CRUD completo)
- **Tratamento de exceções e erros**
- **Segurança web** (prevenção de SQL Injection, criptografia de senhas)
- **HTML5, CSS3 e JavaScript**

Ideal para busca de oportunidades de emprego na área de desenvolvimento web.

---

## 📸 Screenshots

### Página Inicial
![Página Inicial](img/Brasão_do_Rio_Grande_do_Sul.svg.png)

### Painel do Repórter
O repórter pode criar notícias com upload de imagens e gerenciar suas publicações.

### Painel do Administrador
O administrador tem controle total sobre notícias e usuários.

---

## 🐛 Problemas Conhecidos

- Certifique-se de que o XAMPP está rodando antes de acessar a aplicação
- Verifique se o banco de dados foi importado corretamente
- Caso haja erro de conexão, verifique as credenciais em `conexao.php`


## ⚖️ Licença

Este projeto está sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---
