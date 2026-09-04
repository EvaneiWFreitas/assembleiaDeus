<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-file-lines me-2 text-primary"></i><?= esc($titulo) ?></h4>
    <a href="<?= site_url('documentos') ?>" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('documentos/atualizar/' . $registro['id']) : site_url('documentos/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            
            <div class="col-md-6">
                <label class="form-label">Título do Modelo <span class="text-danger">*</span></label>
                <input type="text" name="titulo" class="form-control" required value="<?= esc($registro['titulo'] ?? old('titulo')) ?>" placeholder="Ex.: Certificado de Conclusão de Curso">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                <select name="tipo" class="form-select" required id="tipoDocumento">
                    <option value="">Selecione...</option>
                    <?php foreach ($tipos as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($registro['tipo'] ?? old('tipo')) === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Selecione o tipo para ver as variáveis sugeridas.</div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" <?= ($registro['ativo'] ?? 1) == 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="ativo">Ativo</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="padrao" id="padrao" value="1" <?= ($registro['padrao'] ?? 0) == 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="padrao">Modelo Padrão</label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Conteúdo do Modelo (HTML) <span class="text-danger">*</span></label>
                <textarea name="conteudo" id="editor" class="form-control" rows="15" required><?= esc($registro['conteudo'] ?? old('conteudo')) ?></textarea>
                <div class="form-text">Use o editor para formatar o documento. As variáveis disponíveis aparecem abaixo.</div>
            </div>

            <div class="col-12">
                <label class="form-label">Variáveis Disponíveis (JSON)</label>
                <textarea name="variaveis" id="variaveis" class="form-control font-monospace small" rows="6" placeholder='["nome_aluno", "nome_curso", "data_conclusao", "carga_horaria", "nome_pastor", "nome_igreja"]'><?= esc($registro['variaveis'] ?? old('variaveis')) ?></textarea>
                <div class="form-text">Lista de variáveis que podem ser usadas no modelo. Ex.: <code>{nome_aluno}</code>, <code>{nome_curso}</code>. Estas serão exibidas na visualização para preenchimento.</div>
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('documentos') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<!-- Variáveis sugeridas por tipo -->
<div class="card border-0 shadow-sm" id="sugestoesVariaveis" style="display: none;">
    <div class="card-header bg-light fw-semibold">
        <i class="fa-solid fa-lightbulb me-2 text-warning"></i>Variáveis Sugeridas para o Tipo Selecionado
    </div>
    <div class="card-body">
        <p class="small text-muted mb-2">Clique em uma variável para copiar ao clipboard e colar no editor.</p>
        <div class="flex-wrap gap-1" id="listaVariaveis"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script {csp-script-nonce}>
// Variáveis sugeridas por tipo
const variaveisPorTipo = {
    certificado_curso: [
        'nome_aluno', 'nome_curso', 'data_conclusao', 'carga_horaria', 
        'nome_pastor', 'nome_igreja', 'data_emissao', 'numero_certificado'
    ],
    certificado_consagracao: [
        'nome_obreiro', 'cargo', 'data_consagracao', 'nome_pastor', 
        'nome_igreja', 'data_emissao', 'numero_certificado', 'versiculo'
    ],
    papel_timbrado_ata: [
        'nome_igreja', 'nome_pastor', 'endereco_igreja', 'telefone_igreja',
        'email_igreja', 'data_reuniao', 'tipo_reuniao', 'nome_secretario',
        'nome_presidente', 'pauta', 'local', 'hora_inicio', 'hora_fim'
    ],
    papel_timbrado_convite: [
        'nome_igreja', 'nome_pastor', 'endereco_igreja', 'telefone_igreja',
        'email_igreja', 'nome_convidado', 'nome_igreja_convidada',
        'data_evento', 'hora_evento', 'local_evento', 'tipo_evento', 'data_emissao'
    ],
    outro: [
        'nome_igreja', 'nome_pastor', 'data_emissao', 'documento_numero'
    ]
};

// Inicializar TinyMCE
tinymce.init({
    selector: '#editor',
    height: 400,
    menubar: false,
    plugins: 'lists link image table code fullscreen',
    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | table | link image | code fullscreen',
    content_style: 'body { font-family: Helvetica,Arial,sans-serif; font-size: 14px; }',
    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
        });
    }
});

// Atualizar variáveis sugeridas ao mudar o tipo
document.getElementById('tipoDocumento').addEventListener('change', function() {
    const tipo = this.value;
    const container = document.getElementById('sugestoesVariaveis');
    const lista = document.getElementById('listaVariaveis');
    
    if (tipo && variaveisPorTipo[tipo]) {
        lista.innerHTML = '';
        variaveisPorTipo[tipo].forEach(v => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary-subtle text-primary text-wrap cursor-pointer';
            badge.style.fontSize = '0.85rem';
            badge.textContent = '{' + v + '}';
            badge.title = 'Clique para copiar';
            badge.addEventListener('click', () => {
                navigator.clipboard.writeText('{' + v + '}');
                badge.classList.remove('bg-primary-subtle', 'text-primary');
                badge.classList.add('bg-success-subtle', 'text-success');
                badge.textContent = 'Copiado!';
                setTimeout(() => {
                    badge.classList.remove('bg-success-subtle', 'text-success');
                    badge.classList.add('bg-primary-subtle', 'text-primary');
                    badge.textContent = '{' + v + '}';
                }, 1500);
            });
            lista.appendChild(badge);
        });
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
});

// Se já há tipo selecionado (edição), mostrar sugestões
document.addEventListener('DOMContentLoaded', () => {
    const tipo = document.getElementById('tipoDocumento').value;
    if (tipo) {
        document.getElementById('tipoDocumento').dispatchEvent(new Event('change'));
    }
});

// Preencher variáveis do JSON para sugestão
try {
    const vars = JSON.parse(document.getElementById('variaveis').value || '[]');
    if (vars.length > 0) {
        const container = document.getElementById('sugestoesVariaveis');
        const lista = document.getElementById('listaVariaveis');
        vars.forEach(v => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-info-subtle text-info text-wrap cursor-pointer';
            badge.style.fontSize = '0.85rem';
            badge.textContent = '{' + v + '}';
            badge.title = 'Clique para copiar (variável cadastrada)';
            badge.addEventListener('click', () => {
                navigator.clipboard.writeText('{' + v + '}');
                badge.classList.remove('bg-info-subtle', 'text-info');
                badge.classList.add('bg-success-subtle', 'text-success');
                badge.textContent = 'Copiado!';
                setTimeout(() => {
                    badge.classList.remove('bg-success-subtle', 'text-success');
                    badge.classList.add('bg-info-subtle', 'text-info');
                    badge.textContent = '{' + v + '}';
                }, 1500);
            });
            lista.appendChild(badge);
        });
        container.style.display = 'block';
    }
} catch (e) {}
</script>
<?= $this->endSection() ?>