<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regionais RS - Notícias do Rio Grande do Sul</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-container">
            <div class="logo-section">
                <img src="img/Brasão_do_Rio_Grande_do_Sul.svg.png" alt="Brasão do Rio Grande do Sul" class="brasao-logo">
                <div class="site-title">
                    <h1>Regionais RS</h1>
                    <p>Notícias do Rio Grande do Sul</p>
                </div>
            </div>
            <nav>
                <a href="cadPublico.php">👤 Cadastro</a>
                <a href="cadReporter.php">✍️ Reporter</a>
                <a href="admin_auth.php">👑 Admin</a>
                <a href="login.php">🔐 Login</a>
            </nav>
        </div>
    </header>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'conta_excluida'): ?>
    <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; text-align: center; border-bottom: 1px solid #c8e6c9;">
        ✅ Sua conta foi excluída com sucesso. Obrigado por usar o Regionais RS!
    </div>
    <?php endif; ?>

    <!-- HERO BANNER -->
    <section class="hero">
        <div class="hero-content">
            <h2>Notícias Gaúchas que Conectam Gerações</h2>
            <p>Acompanhe os principais acontecimentos do Rio Grande do Sul em tempo real</p>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="container about-section">
        <h2>Sobre o Regionais RS</h2>
        <div class="about-content">
            <div class="about-text">
                <h3>Sua Janela para o Rio Grande do Sul</h3>
                <p>O Regionais RS é uma plataforma inovadora dedicada a trazer as notícias mais relevantes e atualizadas do Rio Grande do Sul diretamente para você.</p>
                <p>Com repórteres espalhados por todo o estado, garantimos cobertura abrangente dos eventos, políticas e histórias que definem nossa região.</p>
                <p>Somos compromissados em fornecer informação de qualidade que une a comunidade gaúcha.</p>
            </div>
            <img src="img/diadogaucho1.jpg" alt="Dia do Gaúcho" class="about-image">
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="container features">
        <h2>Por Que Nos Escolher?</h2>
        <div class="cards-grid">
            <!-- Card 1 -->
            <div class="card">
                <img src="img/various-people-taking-part-protests.jpg" alt="Eventos Gaúchos" class="card-image">
                <div class="card-content">
                    <h3>🎯 Eventos e Movimentos</h3>
                    <p>Acompanhe em primeira mão todos os eventos, protestos, manifestações e movimentos sociais que movem o Rio Grande do Sul.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="card">
                <img src="img/internationals-people-standing-cafe-drinking-coffee.jpg" alt="Conexão e Comunidade" class="card-image">
                <div class="card-content">
                    <h3>🌐 Conectado e Inclusivo</h3>
                    <p>Somos uma comunidade de leitores, repórteres e entusiastas unidos pela paixão de conhecer e compartilhar as histórias do nosso estado.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="card">
                <img src="img/cultura-gaucha.jpg" alt="Tradição Gaúcha" class="card-image">
                <div class="card-content">
                    <h3>🤠 Tradição Gaúcha</h3>
                    <p>Celebramos a riqueza cultural, tradições e identidade do povo gaúcho em cada notícia que publicamos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="container cta-section">
        <h2>Pronto para se Conectar?</h2>
        <p>Junte-se a nossa comunidade de leitores e repórteres gaúchos</p>
        <div class="cta-buttons">
            <a href="cadPublico.php" class="cta-primary">📝 Criar Conta como Leitor</a>
            <a href="cadReporter.php" class="cta-primary">✍️ Tornar-se Reporter</a>
            <a href="login.php" class="cta-secondary">🔐 Já Tenho Conta</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2026 <strong>Regionais RS</strong> - Notícias do Rio Grande do Sul</p>
        <p>Desenvolvido com 💙 para a comunidade gaúcha</p>
        <p>Bernardo Ribeiro</p>
    </footer>
</body>
</html>