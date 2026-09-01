<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<p class="text-muted mb-4">Nossas células são pequenos grupos que se reúnem semanalmente em casas para comunhão, estudo da Palavra e oração. Encontre a mais próxima de você:</p>

<div class="table-responsive card card-valor p-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr><th>Célula</th><th>Líder</th><th>Endereço</th><th>Dia / Horário</th><th class="text-center">Participantes</th></tr>
        </thead>
        <tbody>
        <?php if (empty($celulas)): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma célula cadastrada no momento.</td></tr>
        <?php endif; ?>
        <?php foreach ($celulas as $c): ?>
            <tr>
                <td class="fw-semibold"><?= esc($c['nome']) ?></td>
                <td><?= esc($c['lider'] ?? '-') ?></td>
                <td><?= esc($c['endereco'] ?: '-') ?></td>
                <td><?= esc(($c['dia_semana'] ?: '-') . ($c['horario'] ? ' · ' . $c['horario'] : '')) ?></td>
                <td class="text-center"><span class="badge badge-azul"><?= (int) ($c['total_participantes'] ?? 0) ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="text-center mt-5">
    <a href="<?= site_url('login') ?>" class="btn btn-azul btn-lg"><i class="fa-solid fa-house-chimney-user me-2"></i>Quero participar de uma célula</a>
</div>

<?= $this->endSection() ?>
