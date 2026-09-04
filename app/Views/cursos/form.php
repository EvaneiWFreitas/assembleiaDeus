<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-graduation-cap me-2 text-primary"></i><?= esc($titulo) ?></h4>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('cursos/atualizar/' . $registro['id']) : site_url('cursos/salvar') ?>" enctype="multipart/form-data" class="row g-3" onsubmit="if(typeof tinymce!=='undefined'){tinymce.triggerSave();}return true;">
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
                <label class="form-label">Foto do Curso (capa)</label>
                <?php if ($ehEdicao && ! empty($registro['foto'])): ?>
                    <div id="fotoCursoAtual" class="mb-2">
                        <img src="<?= base_url('uploads/cursos/' . $registro['foto']) ?>" alt="Foto do curso" class="foto-curso-preview mb-1">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerFotoCurso()"><i class="fa-solid fa-trash me-1"></i>Remover foto atual</button>
                        </div>
                        <input type="hidden" name="remover_foto" id="removerFotoCurso" value="">
                    </div>
                <?php else: ?>
                    <input type="hidden" name="remover_foto" id="removerFotoCurso" value="">
                <?php endif; ?>
                <input type="file" name="foto_curso" id="fotoCursoInput" accept="image/png,image/jpeg,image/webp" class="form-control" onchange="previewFotoCurso(this)">
                <img id="fotoCursoPreviewNova" class="foto-curso-preview mt-2 d-none" alt="Prévia da foto do curso">
            </div>
            <div class="col-md-12">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" id="cursoDescricao" class="form-control" rows="4" placeholder="Descreva brevemente o curso..."><?= esc($registro['descricao'] ?? old('descricao')) ?></textarea>
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
                <form method="post" action="<?= site_url('cursos/adicionarAula/' . $registro['id']) ?>" enctype="multipart/form-data" class="row g-2 mb-3" onsubmit="if(typeof tinymce!=='undefined'){tinymce.triggerSave();}return true;">
                    <?= csrf_field() ?>
                    <div class="col-md-4"><input type="date" name="data" class="form-control form-control-sm"></div>
                    <div class="col-md-8"><input type="text" name="tema" class="form-control form-control-sm" placeholder="Tema da aula *" required></div>
                    <div class="col-12"><input type="url" name="video_url" class="form-control form-control-sm" placeholder="Link da videoaula (YouTube, Vimeo ou arquivo)"></div>
                    <div class="col-12 d-flex align-items-center gap-2">
                        <input type="file" name="foto_aula" accept="image/png,image/jpeg,image/webp" class="form-control form-control-sm" onchange="previewFotoAula(this, 'fotoAulaPreviewAdicionar')">
                        <img id="fotoAulaPreviewAdicionar" class="foto-aula-preview d-none" alt="">
                    </div>
                    <div class="col-12 mt-2">
                        <div class="border rounded-3 p-3 bg-light-subtle">
                            <label class="form-label fw-semibold mb-2" for="novaAula_conteudo">
                                <i class="fa-solid fa-pen-nib me-1 text-primary"></i>Conteúdo da Aula
                            </label>
                            <p class="text-muted small mb-2">Explique passo a passo o conteúdo, o ensino e o resumo da aula.</p>
                            <textarea name="conteudo" id="novaAula_conteudo" class="form-control editor-rico" rows="12" placeholder="Digite aqui o texto da aula..."></textarea>
                        </div>
                    </div>
                    <div class="col-12"><button class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Adicionar Aula</button></div>
                </form>
                <?php endif; ?>

                <?php if (empty($aulas)): ?>
                    <p class="text-muted text-center py-3 mb-0">Nenhuma aula registrada.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($aulas as $a): ?>
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-semibold"><?= esc($a['tema']) ?></span>
                                        <?php if (! empty($a['data'])): ?>
                                            <small class="text-muted ms-2"><i class="fa-regular fa-calendar me-1"></i><?= formatar_data($a['data']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (tem_permissao('cursos', 'editar')): ?>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary btn-editar-aula"
                                            data-id="<?= $a['id'] ?>"
                                            data-tema="<?= esc($a['tema'], 'attr') ?>"
                                            data-data="<?= esc($a['data'] ?? '', 'attr') ?>"
                                            data-video="<?= esc($a['video_url'] ?? '', 'attr') ?>"
                                            data-conteudo="<?= esc($a['conteudo'] ?? '', 'attr') ?>"
                                            data-explicacao="<?= esc($a['explicacao'] ?? '', 'attr') ?>"
                                            data-foto="<?= esc($a['foto'] ?? '', 'attr') ?>"
                                            title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <form method="post" action="<?= site_url('cursos/excluirAula/' . $registro['id'] . '/' . $a['id']) ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-outline-danger" data-confirmar="Excluir a aula <?= esc($a['tema']) ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (! empty($a['foto'])): ?>
                                    <img src="<?= base_url('uploads/aulas/' . $a['foto']) ?>" class="img-fluid rounded mt-2" style="max-height:120px;object-fit:cover" alt="Foto da aula">
                                <?php endif; ?>
                                <?php if (! empty($a['conteudo'])): ?>
                                    <div class="mt-1">
                                        <small class="text-muted d-block mb-1"><?= esc(mb_strimwidth(strip_tags($a['conteudo']), 0, 150, '...')) ?></small>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-editar-conteudo-aula"
                                            data-id="<?= $a['id'] ?>"
                                            data-tema="<?= esc($a['tema'], 'attr') ?>"
                                            data-data="<?= esc($a['data'] ?? '', 'attr') ?>"
                                            data-video="<?= esc($a['video_url'] ?? '', 'attr') ?>"
                                            data-conteudo="<?= esc($a['conteudo'] ?? '', 'attr') ?>"
                                            data-explicacao="<?= esc($a['explicacao'] ?? '', 'attr') ?>"
                                            data-foto="<?= esc($a['foto'] ?? '', 'attr') ?>">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>Editar conteúdo
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <?php if (! empty($a['video_url'])): ?>
                                    <div class="mt-1"><a href="<?= esc($a['video_url']) ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-video me-1"></i>Videoaula</a></div>
                                <?php endif; ?>
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

<!-- Modal Editar Aula -->
<div class="modal fade" id="modalEditarAula" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditarAula" method="post" action="" enctype="multipart/form-data" onsubmit="if(typeof tinymce!=='undefined'){tinymce.triggerSave();}return true;">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i>Editar Aula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="aula_id" id="aulaId">
                    <input type="hidden" name="remover_foto" id="removerFoto" value="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Data</label>
                            <input type="date" name="data" id="editarAula_data" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Tema <span class="text-danger">*</span></label>
                            <input type="text" name="tema" id="editarAula_tema" class="form-control" required maxlength="150">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Link da Videoaula</label>
                            <input type="url" name="video_url" id="editarAula_video_url" class="form-control" placeholder="YouTube, Vimeo ou arquivo">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Foto da Aula</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" name="foto_aula" accept="image/png,image/jpeg,image/webp" id="editarAula_foto" class="form-control" onchange="previewFotoAula(this, 'editarAula_foto_preview')">
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemoverFoto" title="Remover foto" onclick="removerFotoAula()"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            <img id="editarAula_foto_preview" class="foto-aula-preview-grande mt-2" alt="Prévia da foto da aula">
                        </div>
                        <div class="col-12">
                            <label class="form-label small mb-1" for="editarAula_conteudo">Explicação da aula passo a passo / Texto de ensino / Resumo</label>
                            <textarea name="conteudo" id="editarAula_conteudo" class="form-control editor-rico" rows="10" placeholder="Digite aqui o texto da aula: explique passo a passo o conteúdo, o ensino e o resumo..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script {csp-script-nonce}>
// Editor de texto rico para o conteúdo/resumo das aulas
// Obs.: os campos do modal (#editarAula_*) NÃO são inicializados aqui,
// para não criar editores duplicados. Eles são inicializados ao abrir o modal.
if (typeof tinymce !== 'undefined') {
    tinymce.init({
        selector: '.editor-rico:not(#editarAula_conteudo):not(#editarAula_explicacao)',
        height: 350,
        menubar: false,
        plugins: 'lists link image table code fullscreen autolink',
        toolbar: 'undo redo | blocks | fontfamily fontsize | forecolor backcolor | ' +
                 'bold italic underline strikethrough superscript subscript | ' +
                 'alignleft aligncenter alignright alignjustify | ' +
                 'bullist numlist | outdent indent | blockquote | ' +
                 'link unlink | image table | removeformat | code fullscreen',
        font_size_formats: '8pt 9pt 10pt 12pt 14pt 18pt 24pt 30pt 36pt',
        content_style: 'body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; }',
        branding: false,
        promotion: false,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
}

// Modal para editar aula
const modalEl = document.getElementById('modalEditarAula');
const modalEditarAula = new bootstrap.Modal(modalEl);

// Inicializa os editores do modal (apenas quando necessário)
function initEditoresModal() {
    if (typeof tinymce === 'undefined') return;
    ['#editarAula_conteudo'].forEach(sel => {
        if (!tinymce.get(sel.substring(1))) {
            tinymce.init({
                selector: sel,
                height: 220,
                menubar: false,
                plugins: 'lists link image table code fullscreen autolink',
                toolbar: 'undo redo | blocks | fontfamily fontsize | forecolor backcolor | bold italic underline strikethrough superscript subscript | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | blockquote | link unlink | image table | removeformat | code fullscreen',
                font_size_formats: '8pt 9pt 10pt 12pt 14pt 18pt 24pt 30pt 36pt',
                content_style: 'body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; }',
            });
        }
    });
}

// Abrir modal ao clicar em editar
document.querySelectorAll('.btn-editar-aula').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const tema = this.dataset.tema;
        const data = this.dataset.data;
        const video = this.dataset.video;
        const conteudoOriginal = this.dataset.conteudo || '';
        const explicacao = this.dataset.explicacao || '';
        const foto = this.dataset.foto || '';

        // Se houver conteúdo salvo no antigo campo de explicação, exibir junto com o texto principal
        const conteudo = explicacao.trim() !== ''
            ? (conteudoOriginal.trim() !== '' ? conteudoOriginal + '\n\n' : '') + explicacao
            : conteudoOriginal;

        document.getElementById('aulaId').value = id;
        document.getElementById('editarAula_tema').value = tema;
        document.getElementById('editarAula_data').value = data;
        document.getElementById('editarAula_video_url').value = video;

        // Inicializar TinyMCE nos campos do modal se ainda não inicializado
        initEditoresModal();

        // Conteúdo (via editor rico, se disponível)
        if (typeof tinymce !== 'undefined' && tinymce.get('editarAula_conteudo')) {
            tinymce.get('editarAula_conteudo').setContent(conteudo || '');
            tinymce.get('editarAula_conteudo').save();
        } else {
            document.getElementById('editarAula_conteudo').value = conteudo;
        }

        // Explicação removida: agora tudo vai no campo de conteúdo único

        // Foto atual
        const preview = document.getElementById('editarAula_foto_preview');
        if (foto) {
            preview.src = '<?= base_url('uploads/aulas/') ?>' + foto;
            preview.classList.remove('d-none');
        } else {
            preview.src = '';
            preview.classList.add('d-none');
        }
        document.getElementById('removerFoto').value = '';
        document.getElementById('editarAula_foto').value = '';

        // Atualizar action do form
        const form = document.getElementById('formEditarAula');
        form.action = '<?= site_url('cursos/editarAula/' . ($registro['id'] ?? 0) . '/') ?>' + id;

        modalEditarAula.show();
    });
});

// Abrir modal focado no conteúdo ao clicar em "Editar conteúdo"
document.querySelectorAll('.btn-editar-conteudo-aula').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const tema = this.dataset.tema;
        const data = this.dataset.data;
        const video = this.dataset.video;
        const conteudoOriginal = this.dataset.conteudo || '';
        const explicacao = this.dataset.explicacao || '';
        const foto = this.dataset.foto || '';

        const conteudo = explicacao.trim() !== ''
            ? (conteudoOriginal.trim() !== '' ? conteudoOriginal + '\n\n' : '') + explicacao
            : conteudoOriginal;

        document.getElementById('aulaId').value = id;
        document.getElementById('editarAula_tema').value = tema;
        document.getElementById('editarAula_data').value = data;
        document.getElementById('editarAula_video_url').value = video;

        initEditoresModal();

        if (typeof tinymce !== 'undefined' && tinymce.get('editarAula_conteudo')) {
            tinymce.get('editarAula_conteudo').setContent(conteudo || '');
            tinymce.get('editarAula_conteudo').save();
        } else {
            document.getElementById('editarAula_conteudo').value = conteudo;
        }

        const preview = document.getElementById('editarAula_foto_preview');
        if (foto) {
            preview.src = '<?= base_url('uploads/aulas/') ?>' + foto;
            preview.classList.remove('d-none');
        } else {
            preview.src = '';
            preview.classList.add('d-none');
        }
        document.getElementById('removerFoto').value = '';
        document.getElementById('editarAula_foto').value = '';

        const form = document.getElementById('formEditarAula');
        form.action = '<?= site_url('cursos/editarAula/' . ($registro['id'] ?? 0) . '/') ?>' + id;

        modalEditarAula.show();

        setTimeout(() => {
            const textarea = document.getElementById('editarAula_conteudo');
            if (textarea) textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    });
});

// Limpar form ao fechar modal
modalEl?.addEventListener('hidden.bs.modal', function() {
    document.getElementById('formEditarAula').reset();
    document.getElementById('removerFoto').value = '';
    document.getElementById('editarAula_foto_preview').classList.add('d-none');
    // Destruir editores TinyMCE do modal para evitar problemas de reinicialização
    if (typeof tinymce !== 'undefined') {
        ['editarAula_conteudo'].forEach(id => {
            const editor = tinymce.get(id);
            if (editor) editor.destroy();
        });
    }
});

// Prévia da foto ao selecionar um arquivo
function previewFotoAula(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.classList.add('d-none');
    }
}

// Remover a foto atual da aula (no modal de edição)
function removerFotoAula() {
    document.getElementById('removerFoto').value = '1';
    document.getElementById('editarAula_foto').value = '';
    const preview = document.getElementById('editarAula_foto_preview');
    preview.src = '';
    preview.classList.add('d-none');
}

// Prévia da foto do curso ao selecionar um arquivo
function previewFotoCurso(input) {
    const preview = document.getElementById('fotoCursoPreviewNova');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.classList.add('d-none');
    }
}

// Remover a foto atual do curso (no formulário de edição)
function removerFotoCurso() {
    document.getElementById('removerFotoCurso').value = '1';
    document.getElementById('fotoCursoAtual')?.remove();
    const input = document.getElementById('fotoCursoInput');
    if (input) input.value = '';
    const preview = document.getElementById('fotoCursoPreviewNova');
    preview.src = '';
    preview.classList.add('d-none');
}
</script>
<?= $this->endSection() ?>
