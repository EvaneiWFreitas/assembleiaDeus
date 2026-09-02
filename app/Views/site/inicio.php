<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($igreja['nome'] ?? 'Assembleia de Deus') ?> | Início</title>
    <meta name="description" content="<?= esc($igreja['nome'] ?? 'Assembleia de Deus') ?> — <?= esc($cidade) ?><?= $estado ? '/' . esc($estado) : '' ?>. Uma igreja comprometida com a Palavra, com as almas e com o Reino de Deus.">
    <?= favicon_link() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style {csp-style-nonce}>
        :root {
            --azul: #1d4e89;
            --azul-escuro: #143a68;
            --azul-claro: #eef4fb;
            --dourado: #c9a227;
            --texto: #2b3a4a;
            --cinza: #6c7a89;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: var(--texto); background: #fff; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, h5 { font-family: 'Playfair Display', serif; }

        /* ---- Utilitários ---- */
        .text-azul { color: var(--azul) !important; }
        .bg-azul-claro { background: var(--azul-claro); }
        .etiqueta { letter-spacing: .22em; text-transform: uppercase; color: var(--dourado); font-weight: 600; font-size: .78rem; }
        .secao { padding: 5.5rem 0; }
        .titulo-secao { font-weight: 700; font-size: 2.1rem; color: var(--azul-escuro); }
        .titulo-secao::after { content:''; display:block; width:70px; height:4px; background: var(--dourado); margin: .8rem auto 0; border-radius:2px; }
        .sub-secao { color: var(--cinza); max-width: 720px; margin: 0 auto; }

        /* ---- Topbar ---- */
        .topbar { background: var(--azul-escuro); color: rgba(255,255,255,.85); font-size: .82rem; }
        .topbar a { color: rgba(255,255,255,.85); text-decoration: none; transition: color .2s; }
        .topbar a:hover { color: var(--dourado); }
        .topbar .social a { margin-left: .75rem; font-size: .9rem; }

        /* ---- Navbar ---- */
        .navbar-main { background: #fff; box-shadow: 0 2px 14px rgba(20,58,104,.08); }
        .navbar-main .navbar-brand { font-family: 'Playfair Display', serif; font-weight: 700; color: var(--azul-escuro); font-size: 1.2rem; display: flex; align-items: center; gap: .6rem; }
        .navbar-main .navbar-brand img { height: 40px; width: 40px; object-fit: contain; border-radius: 8px; }
        .navbar-main .nav-link { color: var(--texto) !important; font-weight: 500; font-size: .92rem; transition: color .2s; }
        .navbar-main .nav-link:hover, .navbar-main .nav-link.active { color: var(--azul) !important; }
        .btn-azul { background: var(--azul); color: #fff; font-weight: 600; border: none; transition: background .2s; }
        .btn-azul:hover { background: var(--azul-escuro); color: #fff; }
        .btn-outline-azul { border: 2px solid var(--azul); color: var(--azul); font-weight: 600; background: transparent; transition: all .2s; }
        .btn-outline-azul:hover { background: var(--azul); color: #fff; }
        .btn-dourado { background: var(--dourado); color: #fff; font-weight: 600; border: none; transition: all .2s; }
        .btn-dourado:hover { background: #b8891f; color: #fff; transform: translateY(-1px); }

        /* ---- Hero ---- */
        .hero {
            position: relative; color: #fff; overflow: hidden;
            background: linear-gradient(105deg, rgba(20,58,104,.92) 0%, rgba(29,78,137,.80) 50%, rgba(20,58,104,.70) 100%);
        }
        .hero .carousel, .hero .carousel-inner { height: 520px; }
        .hero .carousel-item {
            height: 100%; background-size: cover; background-position: center;
            position: relative; display: flex; align-items: center;
            transition: opacity 1s ease;
        }
        .hero .carousel-item:not(.active) { display: none; }
        .hero .carousel-item.active { display: flex; }
        .hero-overlay {
            position: absolute; inset: 0; z-index: 1;
            background: linear-gradient(105deg, rgba(20,58,104,.92) 0%, rgba(29,78,137,.75) 50%, rgba(20,58,104,.45) 100%);
        }
        .hero-conteudo { position: relative; z-index: 2; width: 100%; }
        .hero::after {
            content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 80px; z-index: 3;
            background: linear-gradient(to top, #fff, transparent);
        }
        .hero h1 { font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 800; line-height: 1.12; }
        .hero h1 a { color: inherit; text-decoration: none; }
        .hero .lead { font-size: 1.15rem; font-weight: 300; color: rgba(255,255,255,.85); max-width: 580px; }
        .hero .linha-ouro { width: 80px; height: 4px; background: var(--dourado); border-radius: 3px; }
        .hero .hero-subtitle { font-family: 'Inter', sans-serif; font-size: .85rem; text-transform: uppercase; letter-spacing: .2em; color: var(--dourado); font-weight: 600; }
        .hero .carousel-control-prev, .hero .carousel-control-next { z-index: 4; width: 9%; }
        @media (max-width: 991.98px) {
            .hero .carousel, .hero .carousel-inner { height: 460px; }
        }

        /* ---- Cards de valor ---- */
        .card-valor {
            border: 1px solid #e7eef7; border-radius: 1.1rem; transition: transform .3s, box-shadow .3s;
            box-shadow: 0 6px 18px rgba(20,58,104,.06); overflow: hidden; height: 100%; background: #fff;
        }
        .card-valor:hover { transform: translateY(-8px); box-shadow: 0 14px 30px rgba(20,58,104,.14); }
        .card-valor .icone {
            width: 70px; height: 70px; border-radius: 50%; display: grid; place-items: center;
            margin: 0 auto 1rem; font-size: 1.6rem; color: var(--azul); background: var(--azul-claro);
        }
        .card-valor h5 { font-weight: 700; color: var(--azul-escuro); }
        .card-valor a { color: var(--azul); font-weight: 600; text-decoration: none; transition: color .2s; }
        .card-valor a:hover { color: var(--azul-escuro); }

        /* ---- Culto Online ---- */
        .card-culto {
            background: #fff; border: 1px solid #e7eef7; border-radius: 1rem;
            color: var(--texto); height: 100%; transition: transform .3s, box-shadow .3s;
        }
        .card-culto:hover { transform: translateY(-6px); box-shadow: 0 12px 26px rgba(20,58,104,.12); }
        .card-culto .icone { font-size: 1.9rem; color: var(--azul); }
        .card-culto .hora { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--azul); }

        /* ---- Contato ---- */
        .item-contato { display: flex; gap: 1rem; align-items: flex-start; }
        .item-contato .icone {
            min-width: 52px; height: 52px; border-radius: 14px; display: grid; place-items: center;
            background: var(--azul-claro); color: var(--azul); font-size: 1.3rem;
        }

        /* ---- Footer ---- */
        footer { background: var(--azul-escuro); color: rgba(255,255,255,.85); }
        footer a { color: #fff; text-decoration: none; transition: color .2s; }
        footer a:hover { color: var(--dourado); }
        footer .rodape-titulo { color: #fff; font-size: .95rem; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 1rem; font-family: 'Inter', sans-serif; font-weight: 600; }
        footer ul { list-style: none; padding: 0; }
        footer ul li { margin-bottom: .5rem; }
        .rodape-linha { border-top: 1px solid rgba(255,255,255,.12); }

        /* ---- Animations ---- */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fadeUp .7s ease-out both; }
        .fade-up-delay-1 { animation-delay: .15s; }
        .fade-up-delay-2 { animation-delay: .3s; }
        .fade-up-delay-3 { animation-delay: .45s; }
    </style>
</head>
<body>

<!-- ======================== TOPBAR ======================== -->
<div class="topbar py-2">
    <div class="container d-flex justify-content-between align-items-center">
        <span>
            <?php if (! empty($endereco) && $endereco !== ','): ?>
                <i class="fa-solid fa-location-dot me-1"></i> <?= esc($endereco) ?><?= $cidade ? ' — ' . esc($cidade) . ($estado ? '/' . esc($estado) : '') : '' ?>
            <?php else: ?>
                <i class="fa-solid fa-location-dot me-1"></i> <?= esc($cidade) ?: 'Consulte-nos' ?>
            <?php endif; ?>
        </span>
        <span class="d-flex align-items-center">
            <?php if (! empty($igreja['whatsapp'])): ?>
                <a href="https://wa.me/55<?= preg_replace('/\D/', '', (string) $igreja['whatsapp']) ?>" target="_blank" rel="noopener" class="me-3">
                    <i class="fa-brands fa-whatsapp me-1"></i><?= esc($igreja['whatsapp']) ?>
                </a>
            <?php endif; ?>
            <a href="<?= site_url('login') ?>"><i class="fa-solid fa-right-to-bracket me-1"></i>Área do Membro</a>
            <span class="social">
                <a href="#" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            </span>
        </span>
    </div>
</div>

<!-- ======================== NAVBAR ======================== -->
<nav class="navbar navbar-expand-lg sticky-top navbar-main py-3">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url() ?>">
            <?php $logo = basename((string) ($igreja['logo'] ?? '')); if ($logo !== '' && is_file(ROOTPATH . 'public/uploads/' . $logo)): ?>
                <img src="<?= base_url('uploads/' . $igreja['logo']) ?>" alt="<?= esc($igreja['nome'] ?? 'Logo') ?>">
            <?php else: ?>
                <i class="fa-solid fa-church text-azul"></i>
            <?php endif; ?>
            <?= esc($igreja['nome'] ?? 'Assembleia de Deus') ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPublico" aria-controls="menuPublico" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="menuPublico">
            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link active" href="<?= site_url() ?>">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="#sobre">Institucional</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('site/ministerios') ?>">Ministérios</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('site/celulas') ?>">Células</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('site/discipulados') ?>">Discipulados</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('site/cursos') ?>">Cursos</a></li>
                <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a class="btn btn-azul px-4" href="<?= site_url('login') ?>"><i class="fa-solid fa-right-to-bracket me-1"></i> Portal de Gestão</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ======================== HERO ======================== -->
<header class="hero">
    <div id="heroCarousel" class="carousel slide <?= count($banners) > 1 ? 'carousel-fade' : '' ?>" data-bs-ride="carousel" data-bs-interval="6000" data-bs-pause="false">
        <div class="carousel-inner">
            <?php if (! empty($banners)): ?>
                <?php $i = 0; foreach ($banners as $b): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>"
                     style="background-image:url('<?= base_url('uploads/banners/' . esc($b['imagem'])) ?>');">
                    <div class="hero-overlay"></div>
                    <div class="container hero-conteudo">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <?php if (! empty($b['link'])): ?><a href="<?= esc($b['link']) ?>" target="_blank" rel="noopener" class="d-block"><?php endif; ?>
                                    <p class="hero-subtitle mb-3 fade-up">Seja muito bem-vindo</p>
                                    <h1 class="mb-3 fade-up fade-up-delay-1"><?= esc($b['titulo']) ?></h1>
                                    <div class="linha-ouro mb-4 fade-up fade-up-delay-1"></div>
                                    <?php if (! empty($b['subtitulo'])): ?>
                                        <p class="lead mb-4 fade-up fade-up-delay-2"><?= esc($b['subtitulo']) ?></p>
                                    <?php endif; ?>
                                <?php if (! empty($b['link'])): ?></a><?php endif; ?>
                                <div class="d-flex flex-wrap gap-3 fade-up fade-up-delay-3">
                                    <a href="#sobre" class="btn btn-dourado btn-lg"><i class="fa-solid fa-church me-2"></i>Conheça Nossa Igreja</a>
                                    <a href="<?= site_url('site/ministerios') ?>" class="btn btn-outline-light btn-lg"><i class="fa-solid fa-hands-praying me-2"></i>Ministérios</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $i++; endforeach; ?>
            <?php else: ?>
                <div class="carousel-item active" style="background-image:url('https://images.unsplash.com/photo-1438032002643-8b0d656fb81b?auto=format&fit=crop&w=1920&q=80');">
                    <div class="hero-overlay"></div>
                    <div class="container hero-conteudo">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <p class="hero-subtitle mb-3 fade-up">Seja muito bem-vindo</p>
                                <h1 class="mb-3 fade-up fade-up-delay-1"><?= esc($igreja['nome'] ?? 'Assembleia de Deus') ?></h1>
                                <div class="linha-ouro mb-4 fade-up fade-up-delay-1"></div>
                                <p class="lead mb-4 fade-up fade-up-delay-2">
                                    Uma igreja comprometida com a Palavra, com as almas e com o crescimento do Reino de Deus<?= $cidade ? ' — reunida em ' . esc($cidade . ($estado ? ' - ' . $estado : '')) : '' ?>.
                                </p>
                                <div class="d-flex flex-wrap gap-3 fade-up fade-up-delay-3">
                                    <a href="#sobre" class="btn btn-dourado btn-lg"><i class="fa-solid fa-church me-2"></i>Conheça Nossa Igreja</a>
                                    <a href="<?= site_url('site/ministerios') ?>" class="btn btn-outline-light btn-lg"><i class="fa-solid fa-hands-praying me-2"></i>Ministérios</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if (count($banners) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
        <?php endif; ?>
    </div>
</header>

<!-- ======================== QUEM SOMOS ======================== -->
<section id="sobre" class="secao bg-azul-claro">
    <div class="container">
        <div class="text-center mb-5">
            <p class="etiqueta mb-2">Quem somos</p>
            <h2 class="titulo-secao">Somos uma igreja evangelizadora,<br class="d-none d-md-block"> discipuladora e frutífera</h2>
            <p class="sub-secao mt-3">
                Comprometidos com a integridade da Palavra de Deus, com a obra missionária e com a expansão do Reino.
                <?php if (! empty($igreja['pastor_responsavel'])): ?>
                    Sob a liderança do <strong><?= esc($igreja['pastor_responsavel']) ?></strong>,
                <?php endif; ?>
                <?php if (! empty($igreja['data_fundacao']) && $igreja['data_fundacao'] !== '0000-00-00'): ?>
                    <?php $anos = (int) date('Y') - (int) date('Y', strtotime($igreja['data_fundacao'])); ?>
                    <?php if ($anos > 0): ?>
                        com mais de <?= $anos ?> anos de história,
                    <?php endif; ?>
                <?php endif; ?>
                por meio de ministérios, células e discipulados, cuidamos de pessoas e formamos vidas para Cristo.
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

<!-- ======================== AGENDA DE CULTOS ======================== -->
<section id="horarios" class="secao">
    <div class="container">
        <div class="text-center mb-5">
            <p class="etiqueta mb-2">Venha adorar conosco</p>
            <h2 class="titulo-secao">Agenda de Reuniões</h2>
            <p class="sub-secao mt-3">Sua presença torna o nosso encontro mais especial. Confira os horários abaixo.</p>
        </div>
        <?php if (!empty($eventos)): ?>
        <div class="row g-4">
            <?php
            $icones = [
                'Culto'   => 'fa-sun',
                'Reunião' => 'fa-people-group',
                'Evento'  => 'fa-calendar-star',
                'Retiro'  => 'fa-mountain-sun',
                'Encontro'=> 'fa-handshake',
                'Outro'   => 'fa-calendar-days',
            ];
            $descDias = ['domingo'=>'Domingo','segunda'=>'Segunda','terça'=>'Terça','quarta'=>'Quarta','quinta'=>'Quinta','sexta'=>'Sexta','sábado'=>'Sábado'];
            ?>
            <?php foreach ($eventos as $e): ?>
            <?php
                $diaSemana = mb_strtolower(date('l', strtotime($e['data_inicio'])));
                $traduzido = $descDias[$diaSemana] ?? ucfirst($diaSemana);
                $dataFmt   = date('d/m', strtotime($e['data_inicio']));
                $horaFmt   = $e['hora_inicio'] ? substr($e['hora_inicio'], 0, 5) : '';
                $icone     = $icones[$e['tipo']] ?? 'fa-calendar-days';
            ?>
            <div class="col-md-4">
                <a href="<?= site_url('site/evento/' . $e['id']) ?>" class="text-decoration-none">
                    <div class="card-culto text-center p-4 h-100">
                        <i class="fa-solid <?= $icone ?> icone mb-3"></i>
                        <h5 class="fw-semibold"><?= esc($e['titulo']) ?></h5>
                        <p class="hora mb-1"><?= $traduzido ?> · <?= $horaFmt ? esc($horaFmt) : esc($dataFmt) ?></p>
                        <?php if (!empty($e['local'])): ?>
                            <p class="small mb-1" style="color:var(--cinza)"><i class="fa-solid fa-location-dot me-1"></i><?= esc($e['local']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($e['responsavel'])): ?>
                            <p class="small mb-0" style="color:var(--cinza)">Resp.: <?= esc($e['responsavel']) ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-culto text-center p-4">
                    <i class="fa-solid fa-sun icone mb-3"></i>
                    <h5 class="fw-semibold">Culto Dominical</h5>
                    <p class="hora mb-1">Domingo · 10h00</p>
                    <p class="small mb-0" style="color:var(--cinza)">Adoração, Palavra e CEIA</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-culto text-center p-4">
                    <i class="fa-solid fa-book-open icone mb-3"></i>
                    <h5 class="fw-semibold">Escola Bíblica Dominical</h5>
                    <p class="hora mb-1">Domingo · 09h00</p>
                    <p class="small mb-0" style="color:var(--cinza)">Ensino bíblico para todas as idades</p>
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
        <?php endif; ?>
    </div>
</section>

<!-- ======================== SERVIÇOS DO SISTEMA ======================== -->
<section class="secao" style="background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);">
    <div class="container">
        <div class="text-center mb-5">
            <p class="etiqueta mb-2">Gestão integrada</p>
            <h2 class="titulo-secao">Nosso Sistema de Gestão</h2>
            <p class="sub-secao mt-3">Ferramentas completas para administração da igreja — membros, finanças, relatórios e muito mais.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 col-lg-2">
                <div class="card card-valor p-3 text-center h-100">
                    <div class="icone" style="width:56px;height:56px;font-size:1.3rem;"><i class="fa-solid fa-users"></i></div>
                    <h6 class="fw-semibold mt-2" style="font-family:'Inter',sans-serif;font-size:.88rem;">Membros</h6>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card card-valor p-3 text-center h-100">
                    <div class="icone" style="width:56px;height:56px;font-size:1.3rem;"><i class="fa-solid fa-coins"></i></div>
                    <h6 class="fw-semibold mt-2" style="font-family:'Inter',sans-serif;font-size:.88rem;">Tesouraria</h6>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card card-valor p-3 text-center h-100">
                    <div class="icone" style="width:56px;height:56px;font-size:1.3rem;"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h6 class="fw-semibold mt-2" style="font-family:'Inter',sans-serif;font-size:.88rem;">Escolas</h6>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card card-valor p-3 text-center h-100">
                    <div class="icone" style="width:56px;height:56px;font-size:1.3rem;"><i class="fa-solid fa-people-group"></i></div>
                    <h6 class="fw-semibold mt-2" style="font-family:'Inter',sans-serif;font-size:.88rem;">Células</h6>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card card-valor p-3 text-center h-100">
                    <div class="icone" style="width:56px;height:56px;font-size:1.3rem;"><i class="fa-solid fa-building"></i></div>
                    <h6 class="fw-semibold mt-2" style="font-family:'Inter',sans-serif;font-size:.88rem;">Congregações</h6>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card card-valor p-3 text-center h-100">
                    <div class="icone" style="width:56px;height:56px;font-size:1.3rem;"><i class="fa-solid fa-chart-line"></i></div>
                    <h6 class="fw-semibold mt-2" style="font-family:'Inter',sans-serif;font-size:.88rem;">Relatórios</h6>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="<?= site_url('login') ?>" class="btn btn-azul btn-lg px-5"><i class="fa-solid fa-right-to-bracket me-2"></i>Acessar o Portal de Gestão</a>
        </div>
    </div>
</section>

<!-- ======================== CONTATO ======================== -->
<section id="contato" class="secao bg-azul-claro">
    <div class="container">
        <div class="text-center mb-5">
            <p class="etiqueta mb-2">Fale conosco</p>
            <h2 class="titulo-secao">Contato e Localização</h2>
        </div>
        <div class="row justify-content-center g-4">
            <div class="col-md-4">
                <div class="item-contato">
                    <div class="icone"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h6 class="fw-semibold mb-1">Endereço</h6>
                        <p class="text-muted mb-0">
                            <?php if (! empty($endereco) && $endereco !== ','): ?>
                                <?= esc($endereco) ?><br>
                            <?php endif; ?>
                            <?= esc(($igreja['bairro'] ?? '')) ?><?= ($igreja['bairro'] ?? '') ? ' — ' : '' ?><?= esc($cidade) ?><?= $estado ? '/' . esc($estado) : '' ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="item-contato">
                    <div class="icone"><i class="fa-brands fa-whatsapp"></i></div>
                    <div>
                        <h6 class="fw-semibold mb-1">WhatsApp</h6>
                        <p class="text-muted mb-0">
                            <?php if (! empty($igreja['whatsapp'])): ?>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', (string) $igreja['whatsapp']) ?>" target="_blank" rel="noopener" class="text-muted text-decoration-none">
                                    <?= esc($igreja['whatsapp']) ?>
                                </a>
                            <?php else: ?>
                                Não informado
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="item-contato">
                    <div class="icone"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <h6 class="fw-semibold mb-1">E-mail</h6>
                        <p class="text-muted mb-0">
                            <?php if (! empty($igreja['email'])): ?>
                                <a href="mailto:<?= esc($igreja['email']) ?>" class="text-muted text-decoration-none">
                                    <?= esc($igreja['email']) ?>
                                </a>
                            <?php else: ?>
                                Não informado
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================== FOOTER ======================== -->
<footer>
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <p class="h5 mb-3">
                    <?php $logo = basename((string) ($igreja['logo'] ?? '')); if ($logo !== '' && is_file(ROOTPATH . 'public/uploads/' . $logo)): ?>
                        <img src="<?= base_url('uploads/' . $igreja['logo']) ?>" alt="" style="height:32px;width:32px;object-fit:contain;border-radius:6px;vertical-align:middle;" class="me-2">
                    <?php else: ?>
                        <i class="fa-solid fa-church text-azul me-2"></i>
                    <?php endif; ?>
                    <?= esc($igreja['nome'] ?? 'Assembleia de Deus') ?>
                </p>
                <p class="small mb-3" style="color:rgba(255,255,255,.65)">
                    Uma igreja comprometida com a Palavra, com as almas e com o crescimento do Reino de Deus.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram fs-5"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f fs-5"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube fs-5"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <p class="rodape-titulo">Navegue</p>
                <ul class="small">
                    <li><a href="<?= site_url() ?>">Início</a></li>
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
                    <li>Domingo · 10h00 — Culto Dominical</li>
                    <li>Quarta · 19h30 — Culto de Oração</li>
                </ul>
            </div>
            <div class="col-lg-3">
                <p class="rodape-titulo">Contato</p>
                <ul class="small">
                    <li>
                        <i class="fa-solid fa-location-dot me-2"></i>
                        <?php if (! empty($endereco) && $endereco !== ','): ?>
                            <?= esc($endereco) ?> — <?= esc($cidade) ?><?= $estado ? '/' . esc($estado) : '' ?>
                        <?php else: ?>
                            <?= esc($cidade) ?: 'Consulte-nos' ?>
                        <?php endif; ?>
                    </li>
                    <?php if (! empty($igreja['whatsapp'])): ?>
                        <li><i class="fa-brands fa-whatsapp me-2"></i><?= esc($igreja['whatsapp']) ?></li>
                    <?php endif; ?>
                    <?php if (! empty($igreja['telefone'])): ?>
                        <li><i class="fa-solid fa-phone me-2"></i><?= esc($igreja['telefone']) ?></li>
                    <?php endif; ?>
                    <?php if (! empty($igreja['email'])): ?>
                        <li><i class="fa-solid fa-envelope me-2"></i><?= esc($igreja['email']) ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="rodape-linha">
        <div class="container py-3 text-center small" style="color:rgba(255,255,255,.55)">
            &copy; <?= date('Y') ?> <?= esc($igreja['nome'] ?? 'Assembleia de Deus') ?> — Todos os direitos reservados.
            <?php if (! empty($igreja['pastor_responsavel'])): ?>
                · <?= esc($igreja['pastor_responsavel']) ?>
            <?php endif; ?>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('a[href^="#"]').forEach(function(a) {
    a.addEventListener('click', function(e) {
        var alvo = document.querySelector(a.getAttribute('href'));
        if (alvo) {
            e.preventDefault();
            var offset = document.querySelector('.navbar-main').offsetHeight + 10;
            var top = alvo.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top: top, behavior: 'smooth' });
        }
    });
});

(function() {
    var navbar = document.querySelector('.navbar-main');
    if (!navbar) return;
    var sections = document.querySelectorAll('section[id]');
    function atualizar() {
        var scrollPos = window.scrollY + navbar.offsetHeight + 20;
        sections.forEach(function(sec) {
            var top = sec.offsetTop;
            var bot = top + sec.offsetHeight;
            var link = navbar.querySelector('a[href="#' + sec.id + '"]');
            if (link) {
                if (scrollPos >= top && scrollPos < bot) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            }
        });
    }
    window.addEventListener('scroll', atualizar, { passive: true });
    atualizar();
})();
</script>
</body>
</html>
