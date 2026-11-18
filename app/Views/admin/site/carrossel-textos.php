<?php
$corSelecionada = static fn (string $campo, string $cor): bool => (string) old($campo, $valores[$campo]) === $cor;
$gruposCores = [
    'hero_cor_eyebrow' => 'Cor do subtítulo',
    'hero_cor_titulo'  => 'Cor do título',
    'hero_cor_texto'   => 'Cor do texto',
];
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url()) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver site
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <form method="post" action="<?= e(url('admin/site/carrossel-textos')) ?>" class="admin-form">
                    <?= App\Core\Csrf::field() ?>

                    <h2 class="h6 admin-card-titulo mb-3">Palavras exibidas</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="campo_eyebrow" class="form-label">Subtítulo (opcional)</label>
                            <input type="text" class="form-control" id="campo_eyebrow" name="hero_eyebrow"
                                   value="<?= e(old('hero_eyebrow', $valores['hero_eyebrow'])) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="campo_titulo" class="form-label">Título (opcional)</label>
                            <input type="text" class="form-control" id="campo_titulo" name="hero_titulo"
                                   value="<?= e(old('hero_titulo', $valores['hero_titulo'])) ?>">
                        </div>
                        <div class="col-12">
                            <label for="campo_texto" class="form-label">Texto (opcional)</label>
                            <textarea class="form-control" id="campo_texto" name="hero_texto" rows="3"><?= e(old('hero_texto', $valores['hero_texto'])) ?></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-text">Deixe em branco os campos que não deseja exibir sobre os slides.</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-palette me-2"></i>Palheta de cores</h2>
                    <div class="row g-4">
                        <?php foreach ($gruposCores as $campo => $rotulo): ?>
                            <div class="col-md-4">
                                <span class="form-label d-block mb-2"><?= e($rotulo) ?></span>
                                <div class="admin-paleta-opcoes">
                                    <?php foreach ($paleta as $cor => $nome): ?>
                                        <label class="admin-paleta-opcao" title="<?= e($nome) ?>">
                                            <input type="radio" name="<?= e($campo) ?>" value="<?= e($cor) ?>"
                                                   <?= $corSelecionada($campo, (string) $cor) ? 'checked' : '' ?>>
                                            <span class="admin-paleta-cor" style="background: <?= e($cor) ?>"></span>
                                            <span class="visually-hidden"><?= e($nome) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                        <a href="<?= e(url('admin/site')) ?>" class="btn btn-ad-outline">
                            <i class="fas fa-images me-1"></i>Gerenciar slides
                        </a>
                        <button type="submit" class="btn btn-ad-primary">
                            <i class="fas fa-check me-1"></i>Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Prévia</h2>
                <div class="admin-hero-preview">
                    <?php if (trim((string) $valores['hero_eyebrow']) !== ''): ?>
                        <p class="small fw-semibold mb-2" style="color: <?= e($valores['hero_cor_eyebrow']) ?>">
                            <?= e($valores['hero_eyebrow']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if (trim((string) $valores['hero_titulo']) !== ''): ?>
                        <p class="h4 fw-bold mb-2" style="color: <?= e($valores['hero_cor_titulo']) ?>">
                            <?= e($valores['hero_titulo']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if (trim((string) $valores['hero_texto']) !== ''): ?>
                        <p class="small mb-0" style="color: <?= e($valores['hero_cor_texto']) ?>">
                            <?= e($valores['hero_texto']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <p class="small text-muted mt-3 mb-0">A prévia usa as cores salvas. Atualize a página após salvar.</p>
            </div>
        </div>
    </div>
</div>
