<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carteirinha — <?= esc($multi ? ($tipo === 'obreiro' ? 'Todos os obreiros' : 'Todos os membros') : $cartoes[0]['nome'], 'attr') ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<?php if ($multi): ?>
<style {csp-style-nonce}>@media print { @page { size: A4 landscape; margin: 4mm; } }</style>
<?php else: ?>
<style {csp-style-nonce}>@media print { @page { size: 100mm 70mm; margin: 0; } }</style>
<?php endif; ?>
<style {csp-style-nonce}>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    html, body {
        width: 100%;
        background: <?= $multi ? '#525252' : '#eef1f6' ?>;
        font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
    }

    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    .barra {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        background: #212529;
        color: #fff;
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: 0 2px 8px rgba(0,0,0,.25);
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .barra h1 { font-size: 15px; font-weight: 600; flex: 1; }
    .barra .btn {
        background: #0d6efd;
        color: #fff;
        border: 0;
        border-radius: 6px;
        padding: 8px 14px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .barra .btn:hover { background: #0b5ed7; }
    .barra .btn.light { background: #6c757d; }
    .barra .btn.light:hover { background: #5c636a; }

    .dica {
        text-align: center;
        padding: 6px 12px;
        font-size: 12px;
        color: #6c757d;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .cartao {
        width: 100mm;
        height: 70mm;
        background: #fff;
        overflow: hidden;
        position: relative;
    }

    /* única carteirinha: papel exatamente no tamanho do cartão */
    body.unica .cartao {
        box-shadow: 0 4px 18px rgba(0,0,0,.18);
        margin: 24px auto;
        border-radius: 6px;
    }
    body.unica .cartao > * { border-radius: 6px; }

    /* impressão em lote: cartões em grade com guias de corte */
    .grade {
        padding: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-start;
    }
    .grade .cartao { outline: 1px dashed #b6b6b6; }

    @media print {
        .barra, .dica { display: none !important; }
        html, body { background: #fff !important; padding: 0 !important; }
        body.unica .cartao { margin: 0; box-shadow: none; border-radius: 0; }
        body.unica .cartao > * { border-radius: 0; }
        .grade { padding: 0; gap: 0; }
        .grade .cartao { outline: 1px dashed #ccc; }
    }
</style>
</head>
<body class="<?= $multi ? 'multi' : 'unica' ?>">

<div class="barra">
    <h1><i class="fa-solid fa-id-card"></i> Carteirinha — <?= esc($multi ? ($tipo === 'obreiro' ? 'Todos os obreiros' : 'Todos os membros') : $cartoes[0]['nome']) ?></h1>
    <a class="btn light" href="<?= site_url('carteirinhas') ?>"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    <button class="btn" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
</div>

<?php if (! $multi): ?>
<div class="dica">
    Papel 10 x 7 cm (ou A4 e recorte). Na caixa de diálogo de impressão, desmarque "Cabeçalhos e rodapés" e use a escala 100%.
</div>
<?php endif; ?>

<div class="<?= $multi ? 'grade' : '' ?>">
    <?php foreach ($cartoes as $p): ?>
        <div class="cartao">
            <?= view('carteirinhas/modelos/' . $modelo, [
                'p'         => $p,
                'igreja'    => $igreja,
                'emitidoEm' => $emitidoEm,
            ]) ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>