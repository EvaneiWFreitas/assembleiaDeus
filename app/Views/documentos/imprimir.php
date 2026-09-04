<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo) ?> - Impressão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <?php $ehCertificado = str_starts_with($registro['tipo'] ?? '', 'certificado'); ?>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
            .documento-container { box-shadow: none; border: none; min-height: 0 !important; }
        }
        <?php if ($ehCertificado): ?>
        /* Certificados: orientação horizontal (paisagem) */
        @page {
            size: A4 landscape;
            margin: 0;
        }
        .documento-container {
            width: 297mm;
            max-width: 297mm;
            min-height: 210mm;
            height: auto;
            margin: 0 auto;
            padding: 15mm;
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background: white;
        }
        @media print {
            .documento-container { width: 297mm; min-height: 210mm; padding: 10mm; }
        }
        <?php else: ?>
        @media print {
            .documento-container { box-shadow: none; border: none; min-height: 0 !important; }
        }
        .documento-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 30mm 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background: white;
            min-height: 297mm;
        }
        <?php endif; ?>
        .label-var { display: block; font-size: 0.8rem; color: #6c757d; }
    </style>
</head>
<body>
    <div class="no-print container my-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light fw-semibold">
                <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Dados para preencher o documento
            </div>
            <div class="card-body">
                <?php if (!empty($variaveis)): ?>
                    <?php $tipoCampo = fn(string $v): string => str_starts_with($v, 'hora') ? 'time' : (str_starts_with($v, 'data') ? 'date' : 'text'); ?>
                    <form id="formPreenchimento" class="row g-3">
                        <?php foreach ($variaveis as $v): ?>
                            <div class="col-md-4">
                                <label class="label-var">{<?= esc($v) ?>}</label>
                                <input <?= $tipoCampo($v) === 'text' ? 'type="text"' : 'type="' . $tipoCampo($v) . '"' ?> name="<?= esc($v) ?>" class="form-control form-control-sm" data-var="<?= esc($v) ?>" placeholder="Preencha {<?= esc($v) ?>}">
                            </div>
                        <?php endforeach; ?>
                        <div class="col-12 d-flex gap-2 pt-2">
                            <button type="button" class="btn btn-primary" id="btnPreencherImprimir"><i class="fa-solid fa-print me-1"></i>Preencher e Imprimir</button>
                            <a href="<?= site_url('documentos/visualizar/' . $registro['id']) ?>" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="mb-0">Este modelo não possui variáveis para preencher.</p>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print me-1"></i>Imprimir</button>
                        <a href="<?= site_url('documentos/visualizar/' . $registro['id']) ?>" class="btn btn-secondary ms-2"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="documento-container" id="documento">
        <?= $registro['conteudo'] ?>
    </div>

    <script>
        const conteudoOriginal = <?= json_encode($registro['conteudo']) ?>;
        const documento = document.getElementById('documento');
        const form = document.getElementById('formPreenchimento');

        document.getElementById('btnPreencherImprimir')?.addEventListener('click', function() {
            if (!form || !documento) return;
            const formData = new FormData(form);
            let html = conteudoOriginal;
            for (const [key, value] of formData.entries()) {
                const valor = value || '{' + key + '}';
                html = html.replace(new RegExp('\\{' + key + '\\}', 'g'), () => valor);
            }
            documento.innerHTML = html;
            // Aguarda a renderização e abre a impressão
            setTimeout(() => window.print(), 100);
        });
    </script>
</body>
</html>
