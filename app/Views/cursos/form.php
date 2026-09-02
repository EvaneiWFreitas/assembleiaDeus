<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-graduation-cap me-2 text-primary"></i><?= esc($titulo) ?></h4>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('cursos/atualizar/' . $registro['id']) : site_url('cursos/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label class="form-label">Nome do Curso <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required value="<?= esc($registro['nome'] ?? old('nome')) ?>" placeholder="Ex.: Formação de Obreiros - Turma 2024">
            </div>
            <div class="col-md-3">
                <label class="form-label">Professor</label>
                <select name="professor_id" class="form-select">
                    <option value="">Selecione...</option>
                    <?php foreach ($membros as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['professor_id'] ?? old('professor_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach ($statusCurso as $s): ?>
                        <option value="<?= esc($s) ?>" <?= ($registro['status'] ?? old('status') ?: 'Planejado') === $s ? 'selected' : '' ?>><?= esc($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Início</label>
                <input type="date" name="data_inicio" class="form-control" value="<?= esc($registro['data_inicio'] ?? old('data_inicio')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Término</label>
                <input type="date" name="data_fim" class="form-control" value="<?= esc($registro['data_fim'] ?? old('data_fim')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Vagas</label>
                <input type="number" name="vagas" min="1" class="form-control" value="<?= esc($registro['vagas'] ?? old('vagas')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Situação</label>
                <select name="ativo" class="form-select">
                    <option value="1" <?= ($registro['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= ($registro['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="2"><?= esc($registro['descricao'] ?? old('descricao')) ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('cursos') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php if ($ehEdicao): ?>
<div class="row g-4">

    <!-- AULAS -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><i class="fa-solid fa-book-open me-2 text-primary"></i>Aulas</div>
            <div class="card-body">
                <?php if (tem_permissao('cursos', 'editar')): ?>
                <form method="post" action="<?= site_url('cursos/adicionarAula/' . $registro['id']) ?>" class="row g-2 mb-3">
                    <?= csrf_field() ?>
                    <div class="col-md-4"><input type="date" name="data" class="form-control form-control-sm"></div>
                    <div class="col-md-8"><input type="text" name="tema" class="form-control form-control-sm" placeholder="Tema da aula *" required></div>
                    <div class="col-12"><input type="url" name="video_url" class="form-control form-control-sm" placeholder="Link da videoaula (YouTube, Vimeo ou arquivo)"></div>
                    <div class="col-12"><textarea name="conteudo" class="form-control form-control-sm" rows="2" placeholder="Orientação de texto / resumo (opcional)"></textarea></div>
                    <div class="col-12"><button class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Adicionar Aula</button></div>
                </form>
                <?php endif; ?>

                <?php if (empty($aulas)): ?>
                    <p class="text-muted text-center py-3 mb-0">Nenhuma aula registrada.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($aulas as $a): ?>
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold"><?= esc($a['tema']) ?></span>
                                    <small class="text-muted"><?= formatar_data($a['data'] ?? null) ?></small>
                                </div>
                                <?php if (! empty($a['conteudo'])): ?><small class="text-muted"><?= esc($a['conteudo']) ?></small><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ALUNOS -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><i class="fa-solid fa-users me-2 text-primary"></i>Alunos Matriculados</div>
            <div class="card-body">
                <?php if (tem_permissao('cursos', 'editar')): ?>
                <form method="post" action="<?= site_url('cursos/matricular/' . $registro['id']) ?>" class="row g-2 mb-3">
                    <?= csrf_field() ?>
                    <div class="col">
                        <select name="membro_id" class="form-select form-select-sm" required>
                            <option value="">Selecione o membro para matricular...</option>
                            <?php foreach ($membros as $id => $nome): ?>
                                <option value="<?= $id ?>"><?= esc($nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto"><button class="btn btn-sm btn-primary"><i class="fa-solid fa-user-plus me-1"></i>Matricular</button></div>
                </form>
                <?php endif; ?>

                <?php if (empty($alunos)): ?>
                    <p class="text-muted text-center py-3 mb-0">Nenhum aluno matriculado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Nome</th><th>Freq.</th><th>Nota</th><th>Status</th><th>Cert.</th><th></th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($alunos as $al): ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($al['nome']) ?></td>
                                    <td colspan="5">
                                        <form method="post" action="<?= site_url('cursos/atualizarAluno/' . $registro['id'] . '/' . $al['id']) ?>" class="row g-1 align-items-center">
                                            <?= csrf_field() ?>
                                            <div class="col-3"><input type="number" name="frequencia" step="0.1" max="100" class="form-control form-control-sm" placeholder="% Freq." value="<?= esc($al['frequencia'] ?? '') ?>"></div>
                                            <div class="col-2"><input type="number" name="nota_final" step="0.1" max="10" class="form-control form-control-sm" placeholder="Nota" value="<?= esc($al['nota_final'] ?? '') ?>"></div>
                                            <div class="col-3">
                                                <select name="status" class="form-select form-select-sm">
                                                    <?php foreach (['Matriculado', 'Cursando', 'Aprovado', 'Reprovado', 'Desistente'] as $st): ?>
                                                        <option value="<?= $st ?>" <?= ($al['status'] ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-auto form-check ms-2 mb-0 text-nowrap">
                                                <input class="form-check-input" type="checkbox" name="certificado" value="1" id="cert<?= $al['id'] ?>" <?= ! empty($al['certificado']) ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="cert<?= $al['id'] ?>">Certificado</label>
                                            </div>
                                            <div class="col-auto d-flex gap-1">
                                                <button class="btn btn-sm btn-outline-primary" title="Salvar"><i class="fa-solid fa-floppy-disk"></i></button>
                                            </div>
                                        </form>
                                        <?php if (tem_permissao('cursos', 'editar')): ?>
                                        <form method="post" action="<?= site_url('cursos/removerAluno/' . $registro['id'] . '/' . $al['id']) ?>" class="mt-1">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-outline-danger" data-confirmar="Remover <?= esc($al['nome']) ?> do curso?"><i class="fa-solid fa-user-minus me-1"></i>Remover</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
