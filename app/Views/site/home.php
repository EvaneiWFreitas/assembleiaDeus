<?php
$slides = array_values(array_filter(
    array_map(static fn ($item): string => trim((string) $item), $slides ?? []),
    static fn (string $item): bool => $item !== ''
));
if ($slides === []) {
    $slides = [asset('images/banner.svg')];
}
$totalSlides = count($slides);
$corSegura = static function (mixed $valor, string $padrao): string {
    $valor = trim((string) $valor);

    return preg_match('/^#[0-9a-f]{6}$/i', $valor) === 1 ? $valor : $padrao;
};
$heroEyebrow = (string) ($config['hero_eyebrow'] ?? 'Ministério Evangélico');
$heroTitulo = (string) ($config['hero_titulo'] ?? $config['ministerio_nome'] ?? 'Assembleia de Deus');
$heroTexto = (string) ($config['hero_texto'] ?? 'Igreja Matriz, congregações e pontos de pregação unidos na fé, na comunhão e no anúncio do evangelho.');
$heroCorEyebrow = $corSegura($config['hero_cor_eyebrow'] ?? '', '#a97f22');
$heroCorTitulo = $corSegura($config['hero_cor_titulo'] ?? '', '#2b608c');
$heroCorTexto = $corSegura($config['hero_cor_texto'] ?? '', '#516b85');
$pastorNome = trim((string) ($config['pastor_presidente'] ?? '')) !== ''
    ? (string) $config['pastor_presidente']
    : 'Pastor Presidente';
$pastorFoto = trim((string) ($config['pastor_foto'] ?? ''));
$diasSemana = [
    'DOMINGO' => 'Domingo',
    'SEGUNDA' => 'Segunda-feira',
    'TERCA'   => 'Terça-feira',
    'QUARTA'  => 'Quarta-feira',
    'QUINTA'  => 'Quinta-feira',
    'SEXTA'   => 'Sexta-feira',
    'SABADO'  => 'Sábado',
];
$historiaImagem = trim((string) ($config['historia_imagem'] ?? ''));
$historiaImagemUrl = $historiaImagem !== '' ? url($historiaImagem) : asset('images/historia.svg');
$historiaTexto = trim((string) ($config['historia_texto'] ?? ''));
$imagemOuPlaceholder = static function (?string $caminho): string {
    $caminho = trim((string) $caminho);

    return $caminho !== '' ? url('uploads/' . $caminho) : url('assets/images/placeholder.svg');
};
$resumo = static function (?string $html, int $limite = 260): string {
    $texto = trim(strip_tags((string) $html));
    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }

    return mb_substr($texto, 0, $limite) . '...';
};
?>

<section class="ad-hero ad-hero-carousel"
         style="--ad-hero-cor-eyebrow: <?= e($heroCorEyebrow) ?>; --ad-hero-cor-titulo: <?= e($heroCorTitulo) ?>; --ad-hero-cor-texto: <?= e($heroCorTexto) ?>;">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6500">
        <div class="carousel-inner">
            <?php foreach ($slides as $indice => $urlSlide): ?>
                <div class="carousel-item <?= $indice === 0 ? 'active' : '' ?>"
                     style="background-image: url('<?= e($urlSlide) ?>');"></div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalSlides > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Slide anterior">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Próximo slide">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
            <div class="carousel-indicators">
                <?php foreach ($slides as $indice => $urlSlide): ?>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= (int) $indice ?>"
                            class="<?= $indice === 0 ? 'active' : '' ?>"
                            <?= $indice === 0 ? 'aria-current="true"' : '' ?>
                            aria-label="Ir para o slide <?= (int) $indice + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="ad-hero-conteudo-wrapper">
        <div class="container ad-hero-conteudo">
            <div class="row justify-content-center text-center">
                <div class="col-lg-9">
                    <?php if (trim($heroEyebrow) !== ''): ?>
                        <p class="ad-hero-eyebrow"><i class="fas fa-cross me-2"></i><?= e($heroEyebrow) ?></p>
                    <?php endif; ?>
                    <?php if (trim($heroTitulo) !== ''): ?>
                        <h1 class="display-4 fw-bold mb-3 ad-hero-titulo"><?= e($heroTitulo) ?></h1>
                    <?php endif; ?>
                    <?php if (trim($heroTexto) !== ''): ?>
                        <p class="lead mb-4 ad-hero-texto"><?= e($heroTexto) ?></p>
                    <?php endif; ?>
                    <a href="#apresentacao" class="btn btn-ad-primary btn-lg me-2 mb-2">Conheça o ministério</a>
                    <a href="<?= e(url('eventos')) ?>" class="btn btn-ad-outline btn-lg mb-2">Veja a programação</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="apresentacao" class="ad-section">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6">
                <span class="ad-eyebrow">Sobre nós</span>
                <h2 class="ad-titulo-secao">Uma comunidade de fé e compromisso</h2>
                <p class="ad-texto">
                    Somos um ministério evangélico dedicado ao ensino da Palavra de Deus, à adoração,
                    à comunhão dos santos e ao cuidado das famílias. Atuamos por meio da Igreja Matriz,
                    das congregações e dos pontos de pregação, com o mesmo propósito: fazer discípulos
                    e servir à comunidade.
                </p>
                <p class="ad-texto mb-4">
                    Nossa identidade é marcada pela organização, pelo respeito e pelo compromisso com
                    tudo o que é feito em nome do Senhor.
                </p>
                <a href="<?= e(url('historia')) ?>" class="btn btn-ad-outline">Saiba mais <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <?php foreach ([
                        ['membros', 'Membros ativos', 'fas fa-users'],
                        ['obreiros', 'Obreiros ativos', 'fas fa-hands-praying'],
                        ['congregacoes', 'Congregações', 'fas fa-place-of-worship'],
                        ['pontos', 'Pontos de pregação', 'fas fa-map-marker-alt'],
                    ] as $indicador): ?>
                        <div class="col-6">
                            <div class="ad-card-indicador">
                                <i class="<?= e($indicador[2]) ?>"></i>
                                <strong><?= (int) ($estatisticas[$indicador[0]] ?? 0) ?></strong>
                                <span><?= e($indicador[1]) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="historia" class="ad-section ad-section-clara">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-5">
                <img src="<?= e($historiaImagemUrl) ?>" alt="Ilustração da história do ministério" class="img-fluid rounded ad-img-secao" loading="lazy">
            </div>
            <div class="col-lg-7">
                <span class="ad-eyebrow"><?= e($config['historia_eyebrow'] ?? 'Nossa história') ?></span>
                <h2 class="ad-titulo-secao"><?= e($config['historia_titulo'] ?? 'O que Deus tem feito entre nós') ?></h2>
                <?php if ($historiaTexto !== ''): ?>
                    <p class="ad-texto"><?= nl2br(e($historiaTexto)) ?></p>
                <?php elseif (isset($paginas['historia'])): ?>
                    <p class="ad-texto"><?= e($resumo($paginas['historia']['conteudo'], 420)) ?></p>
                <?php else: ?>
                    <p class="ad-texto">A história deste ministério será publicada aqui pela administração.</p>
                <?php endif; ?>
                <a href="<?= e(url('historia')) ?>" class="btn btn-ad-outline mt-2">Leia a história completa</a>
            </div>
        </div>
    </div>
</section>

<section id="missao" class="ad-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="ad-eyebrow">O que nos move</span>
            <h2 class="ad-titulo-secao">Missão, visão e valores</h2>
        </div>
        <div class="row g-4">
            <?php foreach ([
                ['fas fa-bullseye', 'Missão', 'Anunciar o evangelho, discipular e cuidar das pessoas em nome de Cristo.'],
                ['fas fa-eye', 'Visão', 'Ser um ministério organizado, fiel e presente em cada congregação e ponto de pregação.'],
                ['fas fa-heart', 'Valores', 'Fidelidade à Palavra, comunhão, respeito, responsabilidade e serviço.'],
            ] as $item): ?>
                <div class="col-md-4">
                    <div class="ad-card h-100 text-center">
                        <div class="ad-card-icone"><i class="<?= e($item[0]) ?>"></i></div>
                        <h4><?= e($item[1]) ?></h4>
                        <p class="mb-0"><?= e($item[2]) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= e(url('missao-visao-valores')) ?>" class="btn btn-ad-outline">Conheça em detalhes</a>
        </div>
    </div>
</section>

<section id="palavra-pastoral" class="ad-section ad-section-clara">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-4 text-center">
                    <div class="ad-pastor-foto">
                        <?php if ($pastorFoto !== ''): ?>
                            <img src="<?= e(url($pastorFoto)) ?>" alt="Foto do <?= e($pastorNome) ?>">
                        <?php else: ?>
                            <i class="fas fa-user-tie"></i>
                        <?php endif; ?>
                    </div>
                    <h4 class="mt-3 mb-1"><?= e($pastorNome) ?></h4>
                    <span class="text-muted">Pastor Presidente</span>
                </div>
            <div class="col-lg-8">
                <span class="ad-eyebrow">Palavra pastoral</span>
                <h2 class="ad-titulo-secao">Uma palavra de significado para sua vida</h2>
                <?php if (isset($paginas['palavra-pastoral'])): ?>
                    <div class="ad-conteudo-pagina"><?= $paginas['palavra-pastoral']['conteudo'] ?></div>
                <?php else: ?>
                    <p class="ad-texto">A palavra pastoral será publicada aqui pela administração.</p>
                <?php endif; ?>
                <a href="<?= e(url('palavra-pastoral')) ?>" class="btn btn-ad-outline mt-2">Leia a mensagem completa</a>
            </div>
        </div>
    </div>
</section>

<section id="estrutura" class="ad-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="ad-eyebrow">Onde atuamos</span>
            <h2 class="ad-titulo-secao">Igreja Matriz, congregações e pontos de pregação</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="ad-card ad-card-atuacao h-100">
                    <div class="ad-card-atuacao-topo">
                        <span class="ad-card-atuacao-icone text-primary"><i class="fas fa-church"></i></span>
                        <span class="ad-card-atuacao-contador"><?= count($igrejas) ?> <?= count($igrejas) === 1 ? 'igreja' : 'igrejas' ?></span>
                    </div>
                    <h3 class="h5">Igreja Matriz</h3>
                    <p class="ad-card-atuacao-descricao mb-0">A sede do ministério e o centro da nossa comunidade de fé.</p>
                    <ul class="ad-lista-atuacao">
                        <?php if ($igrejas === []): ?>
                            <li class="text-muted">Nenhuma igreja matriz cadastrada.</li>
                        <?php else: ?>
                            <?php foreach (array_slice($igrejas, 0, 4) as $igreja): ?>
                                <li>
                                    <i class="fas fa-location-dot"></i>
                                    <span>
                                        <?= e($igreja['nome']) ?>
                                        <small class="d-block text-muted"><?= e($igreja['cidade'] . '/' . $igreja['estado']) ?></small>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                            <?php if (count($igrejas) > 4): ?>
                                <li class="text-muted">e mais <?= count($igrejas) - 4 ?>。</li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="<?= e(url('igrejas')) ?>" class="ad-card-atuacao-link">Ver igrejas <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ad-card ad-card-atuacao h-100">
                    <div class="ad-card-atuacao-topo">
                        <span class="ad-card-atuacao-icone text-info"><i class="fas fa-place-of-worship"></i></span>
                        <span class="ad-card-atuacao-contador"><?= count($congregacoes) ?> <?= count($congregacoes) === 1 ? 'congregação' : 'congregações' ?></span>
                    </div>
                    <h3 class="h5">Congregações</h3>
                    <p class="ad-card-atuacao-descricao mb-0">Comunidades locais unidas no mesmo propósito e missão.</p>
                    <ul class="ad-lista-atuacao">
                        <?php if ($congregacoes === []): ?>
                            <li class="text-muted">Nenhuma congregação cadastrada.</li>
                        <?php else: ?>
                            <?php foreach (array_slice($congregacoes, 0, 4) as $congregacao): ?>
                                <li>
                                    <i class="fas fa-map-pin"></i>
                                    <span>
                                        <?= e($congregacao['nome']) ?>
                                        <small class="d-block text-muted"><?= e($congregacao['cidade'] . '/' . $congregacao['estado']) ?></small>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                            <?php if (count($congregacoes) > 4): ?>
                                <li class="text-muted">e mais <?= count($congregacoes) - 4 ?>。</li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="<?= e(url('congregacoes')) ?>" class="ad-card-atuacao-link">Ver congregações <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ad-card ad-card-atuacao h-100">
                    <div class="ad-card-atuacao-topo">
                        <span class="ad-card-atuacao-icone text-success"><i class="fas fa-map-marker-alt"></i></span>
                        <span class="ad-card-atuacao-contador"><?= count($pontos) ?> <?= count($pontos) === 1 ? 'ponto' : 'pontos' ?></span>
                    </div>
                    <h3 class="h5">Pontos de Pregação</h3>
                    <p class="ad-card-atuacao-descricao mb-0">Presença e anúncio do evangelho em diferentes lugares.</p>
                    <ul class="ad-lista-atuacao">
                        <?php if ($pontos === []): ?>
                            <li class="text-muted">Nenhum ponto de pregação cadastrado.</li>
                        <?php else: ?>
                            <?php foreach (array_slice($pontos, 0, 4) as $ponto): ?>
                                <li>
                                    <i class="fas fa-clock"></i>
                                    <span>
                                        <?= e($ponto['nome']) ?>
                                        <small class="d-block text-muted"><?= e(!empty($ponto['dias_horarios']) ? $ponto['dias_horarios'] : $ponto['endereco']) ?></small>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                            <?php if (count($pontos) > 4): ?>
                                <li class="text-muted">e mais <?= count($pontos) - 4 ?>。</li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="<?= e(url('pontos-pregacao')) ?>" class="ad-card-atuacao-link">Ver pontos <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="horarios" class="ad-section ad-section-clara">
    <div class="container">
        <div class="text-center mb-5">
            <span class="ad-eyebrow"><?= e($config['cultos_eyebrow'] ?? 'Cultos e programação') ?></span>
            <h2 class="ad-titulo-secao"><?= e($config['cultos_titulo'] ?? 'Horários dos cultos e programação semanal') ?></h2>
        </div>
        <?php if ($horarios === []): ?>
            <p class="ad-vazio text-center"><?= e($config['cultos_vazio'] ?? 'Os horários dos cultos serão publicados aqui pela administração.') ?></p>
        <?php else: ?>
            <div class="table-responsive ad-tabela">
                <table class="table align-middle">
                    <thead><tr><th>Dia</th><th>Horário</th><th>Culto / Atividade</th></tr></thead>
                    <tbody>
                        <?php foreach ($horarios as $horario): ?>
                            <tr>
                                <td><strong><?= e($diasSemana[$horario['dia_semana']] ?? ucfirst(mb_strtolower($horario['dia_semana']))) ?></strong></td>
                                <td><?= e(substr((string) $horario['horario'], 0, 5)) ?></td>
                                <td><?= e($horario['descricao'] ?? 'Culto') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="<?= e(url('eventos')) ?>" class="btn btn-ad-primary"><?= e($config['cultos_botao'] ?? 'Veja toda a programação') ?></a>
        </div>
    </div>
</section>

<section id="eventos" class="ad-section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
            <div>
                <span class="ad-eyebrow"><?= e($config['eventos_home_eyebrow'] ?? 'Agenda') ?></span>
                <h2 class="ad-titulo-secao mb-0"><?= e($config['eventos_home_titulo'] ?? 'Próximos eventos') ?></h2>
            </div>
            <a href="<?= e(url('eventos')) ?>" class="btn btn-sm btn-ad-outline"><?= e($config['eventos_home_botao'] ?? 'Ver todos') ?></a>
        </div>
        <?php if ($eventos === []): ?>
            <p class="ad-vazio"><?= e($config['eventos_home_vazio'] ?? 'Nenhum evento programado no momento.') ?></p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($eventos as $evento): ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="ad-card ad-card-evento h-100 p-0">
                            <div class="ad-evento-data">
                                <strong><?= e(date('d', strtotime((string) $evento['data_inicio']))) ?></strong>
                                <span><?= e(substr(dataExtenso((string) $evento['data_inicio']), 3, 3)) ?></span>
                            </div>
                            <div class="p-4 ps-5">
                                <?php if (!empty($evento['tipo_nome'])): ?>
                                    <span class="badge ad-badge mb-2"><?= e($evento['tipo_nome']) ?></span>
                                <?php endif; ?>
                                <h5 class="mb-2"><?= e($evento['titulo']) ?></h5>
                                <p class="small mb-1"><i class="far fa-clock me-2"></i><?= e(substr((string) $evento['data_inicio'], 11, 5)) ?></p>
                                <p class="small mb-3"><i class="fas fa-location-dot me-2"></i><?= e($evento['local_evento'] ?: ($config['eventos_home_local'] ?? 'Local a confirmar')) ?></p>
                                <a href="<?= e(url('eventos/' . $evento['id'])) ?>" class="stretched-link text-decoration-none fw-semibold"><?= e($config['eventos_home_ver_detalhes'] ?? 'Ver detalhes') ?> <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="noticias" class="ad-section ad-section-clara">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
            <div>
                <span class="ad-eyebrow">Acontecendo</span>
                <h2 class="ad-titulo-secao mb-0">Notícias</h2>
            </div>
            <a href="<?= e(url('noticias')) ?>" class="btn btn-sm btn-ad-outline">Todas as notícias</a>
        </div>
        <?php if ($noticias === []): ?>
            <p class="ad-vazio">Nenhuma notícia publicada no momento.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($noticias as $noticia): ?>
                    <div class="col-md-6 col-lg-3">
                        <article class="ad-card h-100 p-0">
                            <?php if (!empty($noticia['imagem'])): ?>
                                <img src="<?= e(url('uploads/' . $noticia['imagem'])) ?>" class="card-img-top ad-card-img" alt="<?= e($noticia['titulo']) ?>" loading="lazy">
                            <?php endif; ?>
                            <div class="p-4">
                                <small class="text-muted"><?= e(dataExtenso((string) ($noticia['publicada_em'] ?? $noticia['created_at']))) ?></small>
                                <h5 class="mt-2"><?= e($noticia['titulo']) ?></h5>
                                <p class="small mb-3"><?= e($noticia['resumo'] ?? '') ?></p>
                                <a href="<?= e(url('noticias/' . $noticia['slug'])) ?>" class="text-decoration-none fw-semibold">Ler notícia <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="galeria" class="ad-section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
            <div>
                <span class="ad-eyebrow">Memórias</span>
                <h2 class="ad-titulo-secao mb-0">Galeria de fotografias</h2>
            </div>
            <a href="<?= e(url('galeria')) ?>" class="btn btn-sm btn-ad-outline">Ver galeria completa</a>
        </div>
        <?php if ($galeria === []): ?>
            <p class="ad-vazio">Nenhuma fotografia publicada no momento.</p>
        <?php else: ?>
            <div class="row g-2">
                <?php foreach ($galeria as $foto): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="<?= e(url('uploads/' . $foto['imagem'])) ?>" data-gallery="galeria" class="d-block ad-galeria-item">
                            <img src="<?= e(url('uploads/' . $foto['imagem'])) ?>" class="img-fluid rounded" alt="<?= e($foto['titulo'] ?? 'Fotografia') ?>" loading="lazy">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="contato" class="ad-section ad-section-clara">
    <div class="container">
        <div class="text-center mb-5">
            <span class="ad-eyebrow">Fale conosco</span>
            <h2 class="ad-titulo-secao">Entre em contato</h2>
        </div>
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <div class="ad-card h-100">
                    <h4>Informações de contato</h4>
                    <ul class="list-unstyled ad-lista-contato">
                        <?php if (($config['ministerio_endereco'] ?? '') !== ''): ?>
                            <li><i class="fas fa-map-marker-alt"></i><span><?= e($config['ministerio_endereco']) ?></span></li>
                        <?php endif; ?>
                        <?php if (($config['ministerio_telefone'] ?? '') !== ''): ?>
                            <li><i class="fas fa-phone"></i><span><?= e($config['ministerio_telefone']) ?></span></li>
                        <?php endif; ?>
                        <?php if (($config['ministerio_whatsapp'] ?? '') !== ''): ?>
                            <li><i class="fab fa-whatsapp"></i><span><?= e($config['ministerio_whatsapp']) ?></span></li>
                        <?php endif; ?>
                        <?php if (($config['ministerio_email'] ?? '') !== ''): ?>
                            <li><i class="fas fa-envelope"></i><span><?= e($config['ministerio_email']) ?></span></li>
                        <?php endif; ?>
                        <?php if (($config['pastor_presidente'] ?? '') !== ''): ?>
                            <li><i class="fas fa-user-tie"></i><span>Pastor Presidente: <?= e($config['pastor_presidente']) ?></span></li>
                        <?php endif; ?>
                    </ul>
                    <?php if (($config['ministerio_endereco'] ?? '') === ''): ?>
                        <p class="small text-muted mb-0">Os dados de contato serão publicados aqui pela administração.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ad-card">
                    <?= App\Core\View::partial('form_contato'); ?>
                </div>
            </div>
        </div>
    </div>
</section>
