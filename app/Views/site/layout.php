<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'Assembleia de Deus') ?></title>
    <?= favicon_link() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --azul: #1d4e89; --azul-escuro: #143a68; --azul-claro: #eef4fb;
            --dourado: #c9a227; --texto: #2b3a4a; --cinza: #6c7a89;
        }
        body { font-family: 'Inter', sans-serif; color: var(--texto); }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
        .topbar { background: var(--azul-escuro); color: rgba(255,255,255,.85); font-size: .82rem; }
        .topbar a { color: rgba(255,255,255,.85); text-decoration: none; margin-left: 1rem; }
        .topbar a:hover { color: var(--dourado); }
        .navbar { background: #fff; box-shadow: 0 2px 14px rgba(20,58,104,.08); }
        .navbar-brand { font-family: 'Playfair Display', serif; font-weight: 700; color: var(--azul-escuro); }
        .navbar .nav-link { color: var(--texto) !important; font-weight: 500; }
        .navbar .nav-link:hover, .navbar .nav-link.active { color: var(--azul) !important; }
        .btn-azul { background: var(--azul); color: #fff; font-weight: 600; }
        .btn-azul:hover { background: var(--azul-escuro); color: #fff; }
        .cabecalho-pagina { background: var(--azul-claro); padding: 3.5rem 0; }
        .cabecalho-pagina h1 { color: var(--azul-escuro); font-weight: 800; }
        .cabecalho-pagina .migalha a { color: var(--azul); text-decoration: none; }
        .cabecalho-pagina .migalha { color: var(--cinza); font-size: .9rem; }
        .card-valor {
            border: 1px solid #e7eef7; border-radius: 1.1rem; transition: transform .25s, box-shadow .25s;
            box-shadow: 0 6px 18px rgba(20,58,104,.06); height: 100%; background: #fff;
        }
        .card-valor:hover { transform: translateY(-6px); box-shadow: 0 14px 30px rgba(20,58,104,.14); }
        .card-valor .icone {
            width: 64px; height: 64px; border-radius: 50%; display: grid; place-items: center;
            font-size: 1.5rem; color: var(--azul); background: var(--azul-claro);
        }
        .etiqueta { letter-spacing: .22em; text-transform: uppercase; color: var(--dourado); font-weight: 600; font-size: .78rem; }
        footer { background: var(--azul-escuro); color: rgba(255,255,255,.85); }
        footer a { color: #fff; text-decoration: none; }
        footer a:hover { color: var(--dourado); }
        .rodape-linha { border-top: 1px solid rgba(255,255,255,.12); }
        .badge-azul { background: var(--azul-claro); color: var(--azul); font-weight: 600; }
    </style>
</head>
<body>
<div class="topbar py-2">
    <div class="container d-flex justify-content-between">
        <span><i class="fa-solid fa-location-dot me-1"></i> <?= esc($igreja['logradouro'] ?? '') ?></span>
        <span><a href="<?= site_url('login') ?>"><i class="fa-solid fa-right-to-bracket me-1"></i>Área do Membro</a></span>
    </div>
</div>

<nav class="navbar navbar-expand-lg sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url() ?>">
            <?php $logo = basename((string) ($igreja['logo'] ?? '')); if ($logo !== '' && is_file(ROOTPATH . 'public/uploads/' . $logo)): ?>
                <img src="<?= base_url('uploads/' . $igreja['logo']) ?>" alt="<?= esc($igreja['nome'] ?? 'Logo') ?>" style="height:40px;width:40px;object-fit:contain;">
            <?php else: ?>
                <i class="fa-solid fa-church text-azul"></i>
            <?php endif; ?>
            <?= esc($nome ?? 'Assembleia de Deus') ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPublico"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-end" id="menuPublico">
            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="<?= site_url() ?>">Início</a></li>
                <li class="nav-item"><a class="nav-link <?= $pagina === 'ministerios' ? 'active' : '' ?>" href="<?= site_url('site/ministerios') ?>">Ministérios</a></li>
                <li class="nav-item"><a class="nav-link <?= $pagina === 'celulas' ? 'active' : '' ?>" href="<?= site_url('site/celulas') ?>">Células</a></li>
                <li class="nav-item"><a class="nav-link <?= $pagina === 'discipulados' ? 'active' : '' ?>" href="<?= site_url('site/discipulados') ?>">Discipulados</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-azul px-4" href="<?= site_url('login') ?>">Portal de Gestão</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="cabecalho-pagina">
    <div class="container">
        <p class="etiqueta mb-2">Conheça o nosso trabalho</p>
        <h1 class="mb-2"><?= esc($titulo) ?></h1>
        <p class="migalha mb-0"><a href="<?= site_url() ?>">Início</a> <i class="fa-solid fa-angle-right mx-1"></i> <?= esc($titulo) ?></p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?= $this->renderSection('conteudo') ?>
    </div>
</section>

<footer>
    <div class="container py-4 text-center small">
        &copy; <?= date('Y') ?> <?= esc($nome ?? 'Assembleia de Deus') ?> — Todos os direitos reservados.
    </div>
    <div class="rodape-linha"></div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
