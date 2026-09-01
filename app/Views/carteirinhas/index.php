<?= $this->extend('layouts/main') ?>
<?= $this->section('styles') ?>
<style {csp-style-nonce}>
    .modelo-rotulo { border-radius: .5rem !important; }
    .modelo-rotulo .modelo-mini {
        display: block; width: 100%; height: 42px; border-radius: .375rem;
        box-shadow: inset 0 0 0 3px rgba(255,255,255,.4);
    }
    .btn-check:checked + .btn-outline-secondary.modelo-rotulo {
        background: #0d6efd; color: #fff; border-color: #0d6efd;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-id-card me-2 text-primary"></i>Carteirinhas</h4>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Configuração</h6>

                <form id="form-carteirinha" method="get" action="<?= site_url('carteirinhas') ?>">
                    <div class="mb-3">
                        <label class="form-label">Tipo de carteirinha</label>
                        <?php foreach (['membro' => 'Membro', 'obreiro' => 'Obreiro / Pastor'] as $valor => $rotulo): ?>
                            <div class="form-check">
                                <input class="form-check-input tipo-check" type="radio" name="tipo" id="tipo-<?= $valor ?>"
                                       value="<?= $valor ?>" <?= $tipo === $valor ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tipo-<?= $valor ?>"><?= $rotulo ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Modelo</label>
                        <div class="row g-2 d-flex flex-wrap modelo-lista">
                            <?php foreach ($modelos as $slug => $m): ?>
                                <div class="col-6">
                                    <input type="radio" class="btn-check modelo-check" name="modelo" id="modelo-<?= $slug ?>"
                                           value="<?= $slug ?>" <?= $modelo === $slug ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-2 modelo-rotulo" for="modelo-<?= $slug ?>">
                                        <span class="modelo-mini" style="background:<?= esc($m['cor']) ?>"></span>
                                        <span class="mt-1 small"><?= esc($m['nome']) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="sel-membro">Pessoa</label>
                        <select class="form-select pessoa-select" id="sel-membro" name="membro"
                                <?= $tipo === 'membro' ? '' : 'disabled' ?>>
                            <option value="">— Selecione o membro —</option>
                            <?php foreach ($membros as $m): ?>
                                <option value="<?= (int) $m['id'] ?>" <?= $tipo === 'membro' && (int) $pessoa_id === (int) $m['id'] ? 'selected' : '' ?>>
                                    <?= esc($m['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select class="form-select pessoa-select mt-1" id="sel-obreiro" name="obreiro"
                                <?= $tipo === 'obreiro' ? '' : 'disabled' ?>>
                            <option value="">— Selecione o obreiro/pastor —</option>
                            <?php foreach ($obreiros as $o): ?>
                                <option value="<?= (int) $o['id'] ?>" <?= $tipo === 'obreiro' && (int) $pessoa_id === (int) $o['id'] ? 'selected' : '' ?>>
                                    <?= esc($o['nome']) ?> (<?= esc($o['cargo']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" id="btn-previa"><i class="fa-solid fa-eye me-1"></i>Prévia</button>
                        <button type="button" class="btn btn-success" id="btn-imprimir"><i class="fa-solid fa-print me-1"></i>Imprimir (1 pessoa)</button>
                        <button type="button" class="btn btn-outline-secondary" id="btn-imprimir-todas"><i class="fa-solid fa-layer-group me-1"></i>Imprimir todas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">Prévia da carteirinha</h6>
                    <span class="small text-muted">Tamanho real: 10 x 7 cm</span>
                </div>
                <div id="area-previa" class="text-center">
                    <?php if ($modelo !== '' && $tipo !== '' && $pessoa_id > 0): ?>
                        <iframe id="if-previa" src="<?= site_url('carteirinhas/imprimir?modelo=' . urlencode($modelo) . '&tipo=' . urlencode($tipo) . '&pessoa_id=' . (int) $pessoa_id) ?>"
                                style="width:100%;height:560px;border:1px solid #dee2e6;border-radius:6px;background:#eef1f6;"></iframe>
                    <?php else: ?>
                        <p class="text-muted my-5">Selecione o modelo e a pessoa para visualizar a carteirinha antes de imprimir.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script {csp-script-nonce}>
    (function () {
        const form        = document.getElementById('form-carteirinha');
        const selMembro   = document.getElementById('sel-membro');
        const selObreiro  = document.getElementById('sel-obreiro');
        const btnPrevia   = document.getElementById('btn-previa');
        const btnImprimir = document.getElementById('btn-imprimir');
        const btnTodas    = document.getElementById('btn-imprimir-todas');

        function tipoAtual() {
            const r = document.querySelector('input[name="tipo"]:checked');
            return r ? r.value : 'membro';
        }

        function modeloAtual() {
            const r = document.querySelector('input[name="modelo"]:checked');
            return r ? r.value : 'classica';
        }

        function atualizarSelects() {
            const t = tipoAtual();
            if (t === 'membro') {
                selMembro.disabled = false;
                selObreiro.disabled = true;
            } else {
                selMembro.disabled = true;
                selObreiro.disabled = false;
            }
        }

        function baseURL() {
            const t = tipoAtual();
            const m = modeloAtual();
            const sel  = t === 'membro' ? selMembro : selObreiro;
            const id = parseInt(sel.value, 10) || 0;
            return form.getAttribute('action').replace(/\/$/, '') +
                '?modelo=' + encodeURIComponent(m) + '&tipo=' + encodeURIComponent(t) + '&pessoa_id=' + id;
        }

        function atualizarPrevia() {
            const url = baseURL();
            const previa = document.getElementById('area-previa');
            if (url.indexOf('pessoa_id=0') !== -1) {
                previa.innerHTML = '<p class="text-muted my-5">Selecione o modelo e a pessoa para visualizar a carteirinha antes de imprimir.</p>';
                return;
            }
            const iframe = document.createElement('iframe');
            iframe.id = 'if-previa';
            iframe.setAttribute('style', 'width:100%;height:560px;border:1px solid #dee2e6;border-radius:6px;background:#eef1f6;');
            iframe.src = url;
            previa.innerHTML = '';
            previa.appendChild(iframe);
        }

        document.querySelectorAll('.tipo-check').forEach(el => el.addEventListener('change', function () {
            atualizarSelects();
            atualizarPrevia();
        }));
        document.querySelectorAll('.modelo-check').forEach(el => el.addEventListener('change', atualizarPrevia));
        selMembro.addEventListener('change', atualizarPrevia);
        selObreiro.addEventListener('change', atualizarPrevia);

        btnPrevia.addEventListener('click', function (e) {
            e.preventDefault();
            atualizarPrevia();
        });

        btnImprimir.addEventListener('click', function () {
            const url = baseURL().replace('/carteirinhas?', '/carteirinhas/imprimir?');
            if (url.indexOf('pessoa_id=0') !== -1) {
                alert('Selecione uma pessoa para imprimir.');
                return;
            }
            window.open(url, '_blank');
        });

        btnTodas.addEventListener('click', function () {
            const url = form.getAttribute('action').replace(/\/$/, '') +
                '/imprimirtudo?modelo=' + encodeURIComponent(modeloAtual()) + '&tipo=' + encodeURIComponent(tipoAtual());
            window.open(url, '_blank');
        });

        atualizarSelects();
    })();
</script>

<?= $this->endSection() ?>