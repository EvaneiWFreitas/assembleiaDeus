<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-house me-2 text-primary"></i>Dashboard</h4>
    <span class="text-muted small"><?= date('d/m/Y') ?></span>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-metrica">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Membros</span>
                        <div class="valor"><?= number_format($cards['membros'], 0, ',', '.') ?></div>
                    </div>
                    <i class="fa-solid fa-people-group fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-metrica" style="border-left-color:#198754;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Visitantes</span>
                        <div class="valor"><?= number_format($cards['visitantes'], 0, ',', '.') ?></div>
                    </div>
                    <i class="fa-solid fa-user-plus fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-metrica" style="border-left-color:#6f42c1;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Congregações</span>
                        <div class="valor"><?= number_format($cards['congregacoes'], 0, ',', '.') ?></div>
                    </div>
                    <i class="fa-solid fa-place-of-worship fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <a href="<?= site_url('usuarios') ?>" class="text-decoration-none" title="Gerenciar Usuários do Sistema">
        <div class="card card-metrica h-100" style="border-left-color:#fd7e14; cursor:pointer;" onmouseover="this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.12)'" onmouseout="this.style.boxShadow=''">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Usuários do Sistema</span>
                        <div class="valor"><?= number_format($cards['usuarios'], 0, ',', '.') ?></div>
                        <span class="small text-primary"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Gerenciar</span>
                    </div>
                    <i class="fa-solid fa-user-gear fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <a href="<?= site_url('dizimos') ?>" class="text-decoration-none" title="Ver Dízimos">
        <div class="card card-metrica h-100" style="border-left-color:#20c997; cursor:pointer;" onmouseover="this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.12)'" onmouseout="this.style.boxShadow=''">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Dízimos (mês)</span>
                        <div class="valor fs-5">R$ <?= number_format($cards['dizimos_mes'], 2, ',', '.') ?></div>
                        <span class="small text-success"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Gerenciar</span>
                    </div>
                    <i class="fa-solid fa-hand-holding-dollar fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <a href="<?= site_url('ofertas') ?>" class="text-decoration-none" title="Ver Ofertas">
        <div class="card card-metrica h-100" style="border-left-color:#0dcaf0; cursor:pointer;" onmouseover="this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.12)'" onmouseout="this.style.boxShadow=''">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Ofertas (mês)</span>
                        <div class="valor fs-5">R$ <?= number_format($cards['ofertas_mes'], 2, ',', '.') ?></div>
                        <span class="small text-info"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Gerenciar</span>
                    </div>
                    <i class="fa-solid fa-gift fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
        </a>
    </div>
</div>

<!-- Gráficos de desempenho -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light fw-semibold">
                <i class="fa-solid fa-chart-line me-2 text-primary"></i>Desempenho Financeiro (últimos 6 meses)
            </div>
            <div class="card-body">
                <canvas id="graficoFinanceiro" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light fw-semibold">
                <i class="fa-solid fa-chart-pie me-2 text-primary"></i>Cadastros do Sistema
            </div>
            <div class="card-body">
                <canvas id="graficoSistema" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-light border">
    <i class="fa-solid fa-circle-info text-primary me-2"></i>
    <strong>Progresso do sistema:</strong> autenticação, usuários, perfis, membros, visitantes, obreiros, ministérios e
    <strong>financeiro</strong> já estão disponíveis. Próximas etapas: Agenda (E4) e Relatórios (E8).
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script {csp-script-nonce}>
const graficoFinanceiro = document.getElementById('graficoFinanceiro');
const graficoSistema = document.getElementById('graficoSistema');

const rotulosFin = <?= json_encode($grafico['rotulos']) ?>;
const entradasFin = <?= json_encode($grafico['entradas']) ?>;
const despesasFin = <?= json_encode($grafico['despesas']) ?>;

if (graficoFinanceiro) {
    new Chart(graficoFinanceiro, {
        type: 'bar',
        data: {
            labels: rotulosFin,
            datasets: [
                {
                    label: 'Receitas',
                    data: entradasFin,
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Despesas',
                    data: despesasFin,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
}

const dadosSistema = {
    labels: [
        'Membros',
        'Visitantes',
        'Obreiros',
        'Congregações',
        'Ministérios',
        'Departamentos',
        'Células',
        'Discipulados',
        'Cursos',
        'Usuários'
    ],
    values: [
        <?= (int) $desempenho['membros'] ?>,
        <?= (int) $desempenho['visitantes'] ?>,
        <?= (int) $desempenho['obreiros'] ?>,
        <?= (int) $desempenho['congregacoes'] ?>,
        <?= (int) $desempenho['ministerios'] ?>,
        <?= (int) $desempenho['departamentos'] ?>,
        <?= (int) $desempenho['celulas'] ?>,
        <?= (int) $desempenho['discipulados'] ?>,
        <?= (int) $desempenho['cursos'] ?>,
        <?= (int) $desempenho['usuarios'] ?>
    ]
};

const cores = [
    '#0d6efd', '#198754', '#fd7e14', '#dc3545',
    '#6f42c1', '#20c997', '#0dcaf0', '#ffc107',
    '#e83e8c', '#6610f2'
];

if (graficoSistema) {
    new Chart(graficoSistema, {
        type: 'doughnut',
        data: {
            labels: dadosSistema.labels,
            datasets: [
                {
                    data: dadosSistema.values,
                    backgroundColor: cores,
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 8 } },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
}
</script>
<?= $this->endSection() ?>