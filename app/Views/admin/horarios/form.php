<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('admin/horarios')) ?>" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card admin-card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="post" action="<?= e($acao) ?>" class="admin-form">
            <?= App\Core\Csrf::field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="campo_local" class="form-label">Local</label>
                    <select class="form-select" id="campo_local" name="local" required>
                        <?php foreach (['IGREJA' => 'Igreja Matriz', 'CONGREGACAO' => 'Congregações', 'PONTO' => 'Pontos de pregação'] as $tipo => $rotuloGrupo): ?>
                            <?php if ($locais[$tipo] === []) { continue; } ?>
                            <optgroup label="<?= e($rotuloGrupo) ?>">
                                <?php foreach ($locais[$tipo] as $opcao): ?>
                                    <option value="<?= e($opcao['valor']) ?>"
                                        <?= (string) old('local', $horario['local']) === $opcao['valor'] ? 'selected' : '' ?>>
                                        <?= e($opcao['rotulo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">O local define onde o culto ou atividade será realizado.</div>
                </div>

                <div class="col-md-3">
                    <label for="campo_dia" class="form-label">Dia da semana</label>
                    <select class="form-select" id="campo_dia" name="dia_semana" required>
                        <?php foreach ($dias as $valor => $rotulo): ?>
                            <option value="<?= e($valor) ?>"
                                <?= (string) old('dia_semana', $horario['dia_semana']) === $valor ? 'selected' : '' ?>>
                                <?= e($rotulo) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="campo_horario" class="form-label">Horário</label>
                    <input type="time" class="form-control" id="campo_horario" name="horario"
                           value="<?= e(old('horario', substr((string) $horario['horario'], 0, 5))) ?>" required>
                </div>

                <div class="col-md-8">
                    <label for="campo_descricao" class="form-label">Culto / Atividade</label>
                    <input type="text" class="form-control" id="campo_descricao" name="descricao"
                           value="<?= e(old('descricao', $horario['descricao'])) ?>"
                           placeholder="Ex.: Culto de adoração" required>
                </div>

                <div class="col-md-2">
                    <label for="campo_duracao" class="form-label">Duração (min)</label>
                    <input type="number" min="1" max="1440" class="form-control" id="campo_duracao" name="duracao_minutos"
                           value="<?= e(old('duracao_minutos', $horario['duracao_minutos'])) ?>">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="campo_ativo"
                               name="ativo" value="1" <?= old('ativo', (string) $horario['ativo']) === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="campo_ativo">Ativo</label>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                <a href="<?= e(url('admin/horarios')) ?>" class="btn btn-ad-outline">Cancelar</a>
                <button type="submit" class="btn btn-ad-primary">
                    <i class="fas fa-check me-1"></i>Salvar horário
                </button>
            </div>
        </form>
    </div>
</div>
