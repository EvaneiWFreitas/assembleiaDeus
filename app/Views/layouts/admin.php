<?php
use App\Core\Acl;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Configuracao;

$usuario = Auth::user();
$perfil = Auth::perfil();
$nomeMinisterio = Configuracao::get('ministerio_nome', 'Assembleia de Deus');
$sigla = Configuracao::get('ministerio_sigla', 'AD');
$logotipo = trim(Configuracao::get('logotipo', ''));
$logoUrl = $logotipo !== '' ? url('uploads/' . $logotipo) : url('assets/images/logo.svg');
$extensaoLogo = strtolower(pathinfo($logotipo !== '' ? $logotipo : 'logo.svg', PATHINFO_EXTENSION));
$logoMime = ['svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp'][$extensaoLogo] ?? 'image/png';
$gruposMenu = $usuario === null ? [] : Acl::menu();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Painel administrativo') ?> | <?= e($nomeMinisterio) ?></title>

    <link rel="icon" href="<?= e($logoUrl) ?>" type="<?= e($logoMime) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar collapse d-lg-block" id="adminSidebar">
        <div class="admin-sidebar-marca">
            <img src="<?= e($logoUrl) ?>" alt="Logotipo" width="42" height="42">
            <div>
                <strong><?= e($sigla) ?></strong>
                <small>Painel administrativo</small>
            </div>
        </div>

        <nav class="admin-menu" aria-label="Menu administrativo">
            <?php if ($gruposMenu === []): ?>
                <div class="admin-menu-grupo">
                    <a class="admin-menu-link" href="<?= e(url('admin/login')) ?>">
                        <i class="fas fa-right-to-bracket"></i><span>Entrar</span>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($gruposMenu as $grupo): ?>
                    <div class="admin-menu-grupo">
                        <div class="admin-menu-titulo"><?= e($grupo['titulo']) ?></div>
                        <?php foreach ($grupo['itens'] as $item): ?>
                            <?php if ($item['disabled']): ?>
                                <span class="admin-menu-link admin-menu-desativado" title="Disponível em etapa futura">
                                    <i class="<?= e($item['icon']) ?>"></i>
                                    <span><?= e($item['label']) ?></span>
                                    <small>em breve</small>
                                </span>
                            <?php else: ?>
                                <a class="admin-menu-link <?= $item['active'] ? 'active' : '' ?>"
                                   href="<?= e($item['href']) ?>">
                                    <i class="<?= e($item['icon']) ?>"></i>
                                    <span><?= e($item['label']) ?></span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </nav>

        <div class="admin-sidebar-rodape">
            <a href="<?= e(url()) ?>"><i class="fas fa-arrow-left me-1"></i>Ver o site</a>
        </div>
    </aside>

    <div class="admin-principal">
        <header class="admin-topbar">
            <button class="btn btn-link admin-menu-toggler d-lg-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#adminSidebar"
                    aria-controls="adminSidebar" aria-expanded="false" aria-label="Abrir menu">
                <i class="fas fa-bars"></i>
            </button>

            <div class="admin-topbar-titulo">
                <span class="d-none d-sm-inline">Sistema assembleiaDeus</span>
                    <strong class="d-sm-none"><?= e($sigla) ?></strong>
            </div>

            <div class="admin-topbar-acoes">
                <?php if ($usuario === null): ?>
                    <a class="btn btn-sm btn-ad-primary" href="<?= e(url('admin/login')) ?>">Entrar</a>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="btn admin-usuario-btn dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (!empty($usuario['foto'])): ?>
                                <img src="<?= e(url('uploads/' . $usuario['foto'])) ?>" alt="Foto do usuario" class="admin-avatar">
                            <?php else: ?>
                                <span class="admin-avatar admin-avatar-inicial">
                                    <?= e(mb_strtoupper(mb_substr((string) $usuario['nome'], 0, 1))) ?>
                                </span>
                            <?php endif; ?>
                            <span class="d-none d-md-inline text-start">
                                <strong><?= e($usuario['nome']) ?></strong>
                                <small><?= e($perfil['nome'] ?? '') ?></small>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end admin-dropdown">
                            <li>
                                <a class="dropdown-item" href="<?= e(url('admin/perfil')) ?>">
                                    <i class="fas fa-user me-2"></i>Meu perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= e(url('admin/trocar-senha')) ?>">
                                    <i class="fas fa-key me-2"></i>Alterar senha
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="<?= e(url('admin/logout')) ?>">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-right-from-bracket me-2"></i>Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <main class="admin-conteudo">
            <?= App\Core\View::partial('alerts_admin') ?>
            <?= $content ?>
        </main>

        <footer class="admin-rodape">
            <span>&copy; <?= date('Y') ?> <?= e($nomeMinisterio) ?></span>
            <span>Gestão Administrativa e Eclesiástica</span>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
