<?php
$valor = static fn (string $campo, mixed $padrao = ''): string => (string) old($campo, $evento[$campo] ?? $padrao);
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('admin/eventos')) ?>" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<form method="post" action="<?= e($acao) ?>" enctype="multipart/form-data" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3">Informações do evento</h2>
            <div class="row g-3">
                <div class="col-lg-8">
                    <label for="campo_titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="campo_titulo" name="titulo"
                           value="<?= e($valor('titulo')) ?>" placeholder="Ex.: Congresso de jovens" required>
                </div>
                <div class="col-lg-4">
                    <label for="campo_tipo" class="form-label">Tipo de evento</label>
                    <select class="form-select" id="campo_tipo" name="tipo_id">
                        <option value="">Não informado</option>
                        <?php foreach ($tipos as $tipo): ?>
                            <option value="<?= (int) $tipo['id'] ?>" <?= $valor('tipo_id') === (string) $tipo['id'] ? 'selected' : '' ?>>
                                <?= e($tipo['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="campo_inicio" class="form-label">Data e hora de início</label>
                    <input type="datetime-local" class="form-control" id="campo_inicio" name="data_inicio"
                           value="<?= e($valor('data_inicio')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="campo_fim" class="form-label">Data e hora de término</label>
                    <input type="datetime-local" class="form-control" id="campo_fim" name="data_fim"
                           value="<?= e($valor('data_fim')) ?>">
                </div>
                <div class="col-md-4">
                    <label for="campo_horario" class="form-label">Horário de exibição</label>
                    <input type="time" class="form-control" id="campo_horario" name="horario"
                           value="<?= e($valor('horario')) ?>">
                    <div class="form-text">Opcional. Exibido na página de detalhes.</div>
                </div>
                <div class="col-md-4">
                    <label for="campo_local" class="form-label">Local</label>
                    <input type="text" class="form-control" id="campo_local" name="local_evento"
                           value="<?= e($valor('local_evento')) ?>" placeholder="Ex.: Santuário principal">
                </div>
                <div class="col-md-4">
                    <label for="campo_endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="campo_endereco" name="endereco"
                           value="<?= e($valor('endereco')) ?>" placeholder="Rua, número, cidade">
                </div>
                <div class="col-md-6">
                    <label for="campo_congregacao" class="form-label">Congregação</label>
                    <select class="form-select" id="campo_congregacao" name="congregacao_id">
                        <option value="">Não informada</option>
                        <?php foreach ($congregacoes as $congregacao): ?>
                            <option value="<?= (int) $congregacao['id'] ?>" <?= $valor('congregacao_id') === (string) $congregacao['id'] ? 'selected' : '' ?>>
                                <?= e($congregacao['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="campo_responsavel" class="form-label">Responsável</label>
                    <select class="form-select" id="campo_responsavel" name="responsavel_id">
                        <option value="">Não informado</option>
                        <?php foreach ($responsaveis as $responsavel): ?>
                            <option value="<?= (int) $responsavel['id'] ?>" <?= $valor('responsavel_id') === (string) $responsavel['id'] ? 'selected' : '' ?>>
                                <?= e($responsavel['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="campo_descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="campo_descricao" name="descricao" rows="6"><?= e($valor('descricao')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3">Foto e publicação</h2>
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="campo_foto" class="form-label">Foto do evento</label>
                    <input type="file" class="form-control" id="campo_foto" name="foto" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPG, PNG ou WebP, até 2 MB. Deixe vazio para manter a foto atual.</div>
                    <?php if (!empty($evento['foto'])): ?>
                        <div class="mt-3">
                            <img src="<?= e(url('uploads/' . $evento['foto'])) ?>" alt="Foto atual do evento" class="admin-upload-preview">
                            <button type="submit" form="form-remover-foto-<?= (int) $evento['id'] ?>"
                                    class="btn btn-sm btn-ad-outline text-danger mt-2"
                                    onclick="return confirm('Remover a foto deste evento?');">
                                <i class="fas fa-trash me-1"></i>Remover foto
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label for="campo_situacao" class="form-label">Situação</label>
                    <select class="form-select" id="campo_situacao" name="situacao" required>
                        <?php
                        $rotulos = [
                            'PLANEJADO'    => 'Planejado',
                            'EM_ANDAMENTO' => 'Em andamento',
                            'CONCLUIDO'    => 'Concluído',
                            'CANCELADO'    => 'Cancelado',
                        ];
                        ?>
                        <?php foreach ($situacoes as $situacao): ?>
                            <option value="<?= e($situacao) ?>" <?= $valor('situacao', 'PLANEJADO') === $situacao ? 'selected' : '' ?>>
                                <?= e($rotulos[$situacao] ?? $situacao) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="campo_destaque"
                               name="destaque" value="1" <?= $valor('destaque', '0') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="campo_destaque">Destacar na agenda</label>
                    </div>

                    <?php if ($podePublicar): ?>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="campo_publicar"
                                   name="publicar_site" value="1" <?= $valor('publicar_site', '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="campo_publicar">Publicar no site</label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-end gap-2">
        <a href="<?= e(url('admin/eventos')) ?>" class="btn btn-ad-outline">Cancelar</a>
        <button type="submit" class="btn btn-ad-primary">
            <i class="fas fa-check me-1"></i>Salvar evento
        </button>
    </div>
</form>

<?php if (!empty($evento['foto'])): ?>
    <form id="form-remover-foto-<?= (int) $evento['id'] ?>"
          method="post" action="<?= e(url('admin/eventos/' . (int) $evento['id'] . '/foto/remover')) ?>" class="d-none">
        <?= App\Core\Csrf::field() ?>
    </form>
<?php endif; ?>
