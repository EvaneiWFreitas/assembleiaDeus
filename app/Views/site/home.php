<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($nome ?? 'Assembleia de Deus') ?> | Início</title>
    <?= favicon_link() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --azul: #1d4e89;
            --azul-escuro: #143a68;
            --azul-claro: #eef4fb;
            --dourado: #c9a227;
            --texto: #2b3a4a;
            --cinza: #6c7a89;
        }
        body { font-family: 'Inter', sans-serif; color: var(--texto); background: #fff; }
        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }
        .text-ouro { color: var(--azul) !important; }
        .text-azul { color: var(--azul) !important; }
        .bg-azul-claro { background: var(--azul-claro); }

        .topbar { background: var(--azul-escuro); color: rgba(255,255,255,.85); font-size: .82rem; }
        .topbar a { color: rgba(255,255,255,.85); text-decoration: none; margin-left: 1rem; }
        .topbar a:hover { color: var(--dourado); }

        .navbar { background: #fff; box-shadow: 0 2px 14px rgba(20,58,104,.08); }
        .navbar-brand { font-family: 'Playfair Display', serif; font-weight: 700; color: var(--azul-escuro); font-size: 1.2rem; }
        .navbar .nav-link { color: var(--texto) !important; font-weight: 500; }
        .navbar .nav-link:hover { color: var(--azul) !important; }
        .btn-ouro { background: var(--azul); color: #fff; font-weight: 600; border: none; }
        .btn-ouro:hover { background: var(--azul-escuro); color: #fff; }
        .btn-outline-azul { border: 2px solid var(--azul); color: var(--azul); font-weight: 600; }
        .btn-outline-azul:hover { background: var(--azul); color: #fff; }

        .hero {
            background: linear-gradient(105deg, rgba(255,255,255,.96) 42%, rgba(238,244,251,.85) 75%, rgba(238,244,251,.4)),
                        url('https://images.unsplash.com/photo-1438032002643-8b0d656fb81b?auto=format&fit=crop&w=1800&q=70') right center/cover no-repeat;
            padding: 6.5rem 0;
            color: var(--texto);
        }
        .hero h1 { font-size: clamp(2.1rem, 5vw, 3.6rem); font-weight: 800; color: var(--azul-escuro); line-height: 1.15; }
        .hero .lead { font-size: 1.2rem; font-weight: 300; color: var(--cinza); }
        .hero .linha-ouro { width: 80px; height: 4px; background: var(--dourado); border-radius: 3px; }
        .etiqueta { letter-spacing: .22em; text-transform: uppercase; color: var(--dourado); font-weight: 600; font-size: .78rem; }
        .verso-card {
            background: #fff; border-radius: 1rem; padding: 1.6rem;
            box-shadow: 0 14px 34px rgba(20,58,104,.10); border-top: 4px solid var(--dourado);
        }
        .verso-card p { font-style: italic; color: var(--cinza); }

        .secao { padding: 5.5rem 0; }
        .titulo-secao { font-weight: 700; font-size: 2.1rem; color: var(--azul-escuro); }
        .titulo-secao::after { content:''; display:block; width:70px; height:4px;
            background: var(--dourado); margin: .8rem auto 0; border-radius:2px; }
        .sub-secao { color: var(--cinza); max-width: 720px; margin: 0 auto; }

        .card-valor {
            border: 1px solid #e7eef7; border-radius: 1.1rem; transition: transform .25s, box-shadow .25s;
            box-shadow: 0 6px 18px rgba(20,58,104,.06); overflow: hidden; height: 100%; background: #fff;
        }
        .card-valor:hover { transform: translateY(-8px); box-shadow: 0 14px 30px rgba(20,58,104,.14); }
        .card-valor .icone {
            width: 70px; height: 70px; border-radius: 50%; display: grid; place-items: center;
            margin: 0 auto 1rem; font-size: 1.6rem; color: var(--azul);
            background: var(--azul-claro);
        }
        .card-valor a { color: var(--azul); font-weight: 600; text-decoration: none; }
        .card-valor a:hover { color: var(--azul-escuro); }

        .card-culto {
            background: #fff; border: 1px solid #e7eef7; border-radius: 1rem;
            color: var(--texto); height: 100%; transition: background .25s, transform .25s, box-shadow .25s;
        }
        .card-culto:hover { transform: translateY(-6px); box-shadow: 0 12px 26px rgba(20,58,104,.12); }
        .card-culto .icone { font-size: 1.9rem; color: var(--azul); }
        .card-culto .hora { font-family: 'Playfair Display', serif; font-size: 1.15rem; color: var(--azul); }

        .item-contato { display: flex; gap: 1rem; align-items: flex-start; }
        .item-contato .icone {
            min-width: 52px; height: 52px; border-radius: 14px; display: grid; place-items: center;
            background: var(--azul-claro); color: var(--azul); font-size: 1.3rem;
        }

        footer { background: var(--azul-escuro); color: rgba(255,255,255,.85); }
        footer a { color: #fff; text-decoration: none; }
        footer a:hover { color: var(--dourado); }
        footer .rodape-titulo { color: #fff; font-size: .95rem; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 1rem; }
        footer ul { list-style: none; padding: 0; }
        footer ul li { margin-bottom: .5rem; }
        .rodape-linha { border-top: 1px solid rgba(255,255,255,.12); }
    </style>
</head>
<body>
<div class="topbar py-2">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-location-dot me-1"></i> <?= esc(trim(($igreja['logradouro'] ?? '') . ', ' . ($igreja['numero'] ?? ''), ', ')) ?: 'Consulte-nos' ?></span>
        <span>
            <?php if (!empty($igreja['whatsapp'])): ?><a href="https://wa.me/55<?= preg_replace('/\D/', '', (string) $igreja['whatsapp']) ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp me-1"></i><?= esc($igreja['whatsapp']) ?></a><?php endif; ?>
            <a href="<?= site_url('login') ?>"><i class="fa-solid fa-right-to-bracket me-1"></i>Área do Membro</a>
        </span>
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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPublico">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="menuPublico">
            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="#sobre">Institucional</a></li>
                <li class="nav-item"><a class="nav-link" href="#horarios">Agenda</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('site/ministerios') ?>">Ministérios</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('site/celulas') ?>">Células</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('site/discipulados') ?>">Discipulados</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('site/cursos') ?>">Cursos</a></li>
                <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a class="btn btn-ouro px-4" href="<?= site_url('login') ?>"><i class="fa-solid fa-right-to-bracket me-1"></i> Portal de Gestão</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero position-relative">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <p class="etiqueta mb-3">Seja muito bem-vindo</p>
                <h1 class="mb-3">Uma igreja comprometida<br>com a Palavra e com as pessoas</h1>
                <div class="linha-ouro mb-4"></div>
                <p class="lead mb-4">Somos uma comunidade evangelizadora, discipuladora e frutífera<?= $cidade ? ', reunida em ' . esc($cidade . ($estado ? ' - ' . $estado : '')) : '' ?>. Venha participar — há um lugar preparado para você e para toda a sua família.</p>
                <a href="#horarios" class="btn btn-ouro btn-lg me-2 mb-2"><i class="fa-regular fa-clock me-2"></i>Horários dos Cultos</a>
                <a href="#contato" class="btn btn-outline-azul btn-lg mb-2"><i class="fa-solid fa-map-location-dot me-2"></i>Como Chegar</a>
            </div>
            <div class="col-lg-5">
                <div class="verso-card">
                    <i class="fa-solid fa-quote-left text-azul mb-3"></i>
                    <p class="mb-2">“Porque onde estiverem dois ou três reunidos em meu nome, aí estou eu no meio deles.”</p>
                    <p class="text-azul small fw-semibold mb-0">— Mateus 18:20</p>
                </div>
            </div>
        </div>
    </div>
</header>

<section id="sobre" class="secao bg-azul-claro">
    <div class="container">
        <div class="text-center mb-5">
            <p class="etiqueta mb-2">Quem somos</p>
            <h2 class="titulo-secao">Somos uma igreja evangelizadora, discipuladora e frutífera</h2>
            <p class="sub-secao mt-3">
                Comprometidos com a integridade da Palavra de Deus, com a obra missionária e com a expansão do Reino.
                Por meio de ministérios, células e discipulados, cuidamos de pessoas e formamos vidas para Cristo.
            </p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="card card-valor p-4">
                    <div class="icone"><i class="fa-solid fa-hands-praying"></i></div>
                    <h5 class="fw-semibold">Ministérios</h5>
                    <p class="text-muted mb-3">Louvor, crianças, jovens, missões e muito mais — cada dom a serviço do Reino.</p>
                    <a href="<?= site_url('site/ministerios') ?>">Conhecer ministérios <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-valor p-4">
                    <div class="icone"><i class="fa-solid fa-people-group"></i></div>
                    <h5 class="fw-semibold">Células</h5>
                    <p class="text-muted mb-3">Pequenos grupos que se reúnem em casas, levando comunhão e cuidado de perto até você.</p>
                    <a href="<?= site_url('site/celulas') ?>">Encontrar uma célula <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-valor p-4">
                    <div class="icone"><i class="fa-solid fa-book-bible"></i></div>
                    <h5 class="fw-semibold">Discipulados</h5>
                    <p class="text-muted mb-3">Cresça na fé com ensino bíblico e acompanhamento pessoal de um discipulador.</p>
                    <a href="<?= site_url('site/discipulados') ?>">Participar <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-valor p-4">
                    <div class="icone"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h5 class="fw-semibold">Cursos</h5>
                    <p class="text-muted mb-3">Estude online com videoaulas e orientação de texto — cadastre-se agora!</p>
                    <a href="<?= site_url('site/cursos') ?>">Ver cursos <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="horarios" class="secao">
    <div class="container">
        <div class="text-center mb-5">
            <p class="etiqueta mb-2">Culto online e presencial</p>
            <h2 class="titulo-secao">Agenda de Reuniões</h2>
            <p class="sub-secao mt-3">Venha adorar conosco. Sua presença torna o nosso encontro mais especial.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-culto text-center p-4">
                    <i class="fa-solid fa-sun icone mb-3"></i>
                    <h5 class="fw-semibold">Culto Dominical</h5>
                    <p class="hora mb-1">Domingo · 10h00 e 18h00</p>
                    <p class="small mb-0" style="color:var(--cinza)">Adoração, Palavra e Ceia</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-culto text-center p-4">
                    <i class="fa-solid fa-book-open icone mb-3"></i>
                    <h5 class="fw-semibold">Escola Bíblica</h5>
                    <p class="hora mb-1">Domingo · 09h00</p>
                    <p class="small mb-0" style="color:var(--cinza)">Ensino para todas as idades</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-culto text-center p-4">
                    <i class="fa-solid fa-fire icone mb-3"></i>
                    <h5 class="fw-semibold">Culto de Oração</h5>
                    <p class="hora mb-1">Quarta-feira · 19h30</p>
                    <p class="small mb-0" style="color:var(--cinza)">Intercessão e louvor</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contato" class="secao">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="titulo-secao">Contato e Localização</h2>
        </div>
        <div class="row justify-content-center g-4">
            <div class="col-md-4">
                <div class="item-contato">
                    <div class="icone"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h6 class="fw-semibold mb-1">Endereço</h6>
                        <p class="text-muted mb-0">
                            <?= esc(trim(($igreja['logradouro'] ?? '') . ', ' . ($igreja['numero'] ?? ''), ', ')) ?: 'Consulte-nos' ?><br>
                            <?= esc(($igreja['bairro'] ?? '')) ?><?= $igreja['bairro'] ?? '' ? ' — ' : '' ?><?= esc(($igreja['cidade'] ?? '')) ?><?= $igreja['estado'] ?? '' ? '/' . esc($igreja['estado']) : '' ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="item-contato">
                    <div class="icone"><i class="fa-brands fa-whatsapp"></i></div>
                    <div>
                        <h6 class="fw-semibold mb-1">WhatsApp</h6>
                        <p class="text-muted mb-0"><?= esc($igreja['whatsapp'] ?? '(00) 00000-0000') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="item-contato">
                    <div class="icone"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <h6 class="fw-semibold mb-1">E-mail</h6>
                        <p class="text-muted mb-0"><?= esc($igreja['email'] ?? 'contato@igreja.com') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php
            $enderecoCompleto = trim(
                ($igreja['logradouro'] ?? '')
                . ', ' . ($igreja['numero'] ?? '')
                . ' - ' . ($igreja['bairro'] ?? '')
                . ', ' . ($igreja['cidade'] ?? '')
                . ' - ' . ($igreja['estado'] ?? '')
            , ', -');
        ?>
        <?php if (! empty($enderecoCompleto)): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-lg-10">
                <div class="rounded-4 overflow-hidden shadow-sm border">
                    <iframe
                        src="https://maps.google.com/maps?q=<?= urlencode($enderecoCompleto) ?>&t=&z=16&ie=UTF8&iwloc=&output=embed"
                        width="100%"
                        height="350"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Localização no mapa">
                    </iframe>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<footer>
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <p class="h5 mb-3"><i class="fa-solid fa-church text-azul me-2"></i><?= esc($nome ?? 'Assembleia de Deus') ?></p>
                <p class="small mb-0" style="color:rgba(255,255,255,.65)">Uma igreja comprometida com a Palavra, com as almas e com o crescimento do Reino de Deus.</p>
            </div>
            <div class="col-lg-2 col-6">
                <p class="rodape-titulo">Navegue</p>
                <ul class="small">
                    <li><a href="<?= site_url('site/ministerios') ?>">Ministérios</a></li>
                    <li><a href="<?= site_url('site/celulas') ?>">Células</a></li>
                    <li><a href="<?= site_url('site/discipulados') ?>">Discipulados</a></li>
                    <li><a href="<?= site_url('login') ?>">Portal de Gestão</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-6">
                <p class="rodape-titulo">Reuniões</p>
                <ul class="small">
                    <li>Domingo · 09h00 — Escola Bíblica</li>
                    <li>Domingo · 10h00 e 18h00 — Culto</li>
                    <li>Quarta · 19h30 — Oração</li>
                </ul>
            </div>
            <div class="col-lg-3">
                <p class="rodape-titulo">Contato</p>
                <ul class="small">
                    <li><i class="fa-solid fa-location-dot me-2"></i><?= esc(trim(($igreja['logradouro'] ?? '') . ', ' . ($igreja['numero'] ?? ''), ', ')) ?: 'Consulte-nos' ?></li>
                    <li><i class="fa-brands fa-whatsapp me-2"></i><?= esc($igreja['whatsapp'] ?? '-') ?></li>
                    <li><i class="fa-solid fa-envelope me-2"></i><?= esc($igreja['email'] ?? '-') ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="rodape-linha">
        <div class="container py-3 text-center small" style="color:rgba(255,255,255,.55)">
            &copy; <?= date('Y') ?> <?= esc($nome ?? 'Assembleia de Deus') ?> — Todos os direitos reservados.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Rolagem suave para as âncoras do menu
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const alvo = document.querySelector(a.getAttribute('href'));
        if (alvo) { e.preventDefault(); alvo.scrollIntoView({ behavior: 'smooth' }); }
    });
});
</script>
</body>
</html>
