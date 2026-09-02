<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-user-graduate me-2 text-primary"></i>Inscritos em Cursos Online</h4>
    <a href="<?= site_url('cursos') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small">Curso</label>
                <select name="curso_id" class="form-select form-select-sm">
                    <option value="">Todos os cursos</option>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $filtroCurso == $c['id'] ? 'selected' : '' ?>><?= esc($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="Matriculado" <?= $filtroStatus === 'Matriculado' ? 'selected' : '' ?>>Matriculado</option>
                    <option value="Cursando" <?= $filtroStatus === 'Cursando' ? 'selected' : '' ?>>Cursando</option>
                    <option value="Concluído" <?= $filtroStatus === 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                    <option value="Evadido" <?= $filtroStatus === 'Evadido' ? 'selected' : '' ?>>Evadido</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-sm btn-primary"><i class="fa-solid fa-filter me-1"></i>Filtrar</button>
                <a href="<?= site_url('cursos/inscricoes') ?>" class="btn btn-sm btn-outline-secondary">Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="fa-solid fa-users me-2 text-primary"></i>
        <?= count($inscricoes) ?> inscrito(s) encontrado(s)
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Curso</th>
                    <th>Aluno</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Data Inscrição</th>
                    <th>Status</th>
                    <th>Certificado</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($inscricoes)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhuma inscrição encontrada.</td></tr>
            <?php endif; ?>
            <?php foreach ($inscricoes as $i): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($i['curso_nome']) ?></td>
                    <td><?= esc($i['aluno_nome']) ?></td>
                    <td><small class="text-muted"><?= esc($i['aluno_email']) ?></small></td>
                    <td><?= esc($i['aluno_telefone'] ?? '-') ?></td>
                    <td><?= formatar_data($i['data_inscricao'] ?? null) ?></td>
                    <td>
                        <?php
                        $cores = [
                            'Matriculado' => 'info',
                            'Cursando'    => 'primary',
                            'Concluído'   => 'success',
                            'Evadido'     => 'danger',
                        ];
                        $cor = $cores[$i['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $cor ?>-subtle text-<?= $cor ?>"><?= esc($i['status']) ?></span>
                    </td>
                    <td>
                        <?= $i['certificado'] ? '<i class="fa-solid fa-certificate text-success" title="Certificado emitido"></i>' : '<i class="fa-solid fa-minus text-muted"></i>' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
