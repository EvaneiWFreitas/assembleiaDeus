<?php
$fotoUrl = '';
if ($p['foto'] !== '') {
    $nomeFoto = basename($p['foto']);
    if (is_file(ROOTPATH . 'public/uploads/' . $p['foto_dir'] . '/' . $nomeFoto)) {
        $fotoUrl = base_url('uploads/' . $p['foto_dir'] . '/' . $nomeFoto);
    }
}
$logo = basename((string) ($igreja['logo'] ?? ''));
$logoUrl = $logo !== '' && is_file(ROOTPATH . 'public/uploads/' . $logo)
    ? base_url('uploads/' . $igreja['logo'])
    : '';
$cidadeUf = trim(($igreja['cidade'] ?? '') . '/' . ($igreja['estado'] ?? ''), ' /');
?>
<style {csp-style-nonce}>
    .ml-elegante { width: 100%; height: 100%; background: #faf5e9; font-family: Georgia, 'Times New Roman', serif; position: relative; color: #4a3513; overflow: hidden; }
    .ml-elegante::before { content: ''; position: absolute; inset: 6px; border: 1px solid #c9a45c; pointer-events: none; }
    .ml-elegante .topo { position: absolute; top: 14px; left: 0; right: 0; text-align: center; }
    .ml-elegante .topo .igreja { font-size: 11px; letter-spacing: 2.5px; text-transform: uppercase; color: #8a6a2f; }
    .ml-elegante .topo .orn { font-size: 13px; color: #c9a45c; line-height: 1; }

    .ml-elegante .corpo { position: absolute; top: 52px; left: 0; right: 0; bottom: 44px; display: flex; align-items: center; gap: 16px; padding: 0 20px; }
    .ml-elegante .foto { width: 118px; height: 118px; border-radius: 50%; border: 2px solid #c9a45c; outline: 3px double #c9a45c; overflow: hidden; background: #fff; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
    .ml-elegante .foto img { width: 100%; height: 100%; object-fit: cover; }
    .ml-elegante .foto .ini { font-size: 44px; color: #c9a45c; font-family: Georgia, serif; }
    .ml-elegante .info { flex: 1; min-width: 0; }
    .ml-elegante .info .nome { font-size: 14px; font-style: italic; line-height: 1.2; }
    .ml-elegante .info .cargo { font-size: 8.5px; letter-spacing: 3px; text-transform: uppercase; color: #8a6a2f; margin-top: 3px; }
    .ml-elegante .info .divis { height: 1px; background: linear-gradient(90deg, #c9a45c, transparent); margin: 7px 0; }
    .ml-elegante .info .linha { display: flex; justify-content: space-between; gap: 10px; font-size: 9px; line-height: 1.6; }
    .ml-elegante .info .linha b { font-weight: normal; text-transform: uppercase; letter-spacing: .5px; color: #a3803a; font-size: 7.5px; }

    .ml-elegante .rodape { position: absolute; left: 0; right: 0; bottom: 0; height: 44px; background: #8a6a2f; color: #fdf8ec; display: flex; align-items: center; justify-content: space-between; padding: 0 22px; font-size: 8px; }
    .ml-elegante .rodape b { color: #ffe8b3; }
    .ml-elegante .rodape .local { text-align: right; }
</style>

<div class="ml-elegante">
    <div class="topo">
        <div class="orn">&#10086;</div>
        <div class="igreja"><?= esc($igreja['nome'] ?? '') ?></div>
    </div>

    <div class="corpo">
        <div class="foto">
            <?php if ($fotoUrl !== ''): ?>
                <img src="<?= esc($fotoUrl) ?>" alt="">
            <?php else: ?>
                <div class="ini"><?= esc($p['foto_inicial']) ?></div>
            <?php endif; ?>
        </div>

        <div class="info">
            <div class="nome"><?= esc($p['nome']) ?></div>
            <div class="cargo"><?= esc($p['rotulo']) ?></div>
            <div class="divis"></div>

            <div class="linha"><b>CPF</b><span><?= esc($p['cpf']) ?></span></div>
            <div class="linha"><b>RG</b><span><?= $p['rg'] !== '' ? esc($p['rg']) : '—' ?></span></div>
            <div class="linha"><b>Nascimento</b><span><?= esc($p['nascimento']) ?></span></div>
            <?php if ($p['congregacao'] !== ''): ?>
                <div class="linha"><b>Congregação</b><span><?= esc($p['congregacao']) ?></span></div>
            <?php endif; ?>
            <?php if ($p['numero_registro'] !== ''): ?>
                <div class="linha"><b>Registro</b><span>Nº <?= esc($p['numero_registro']) ?></span></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="rodape">
        <div><b><?= esc($p['rotulo']) ?></b> • <?= esc($p['tipo_membro']) ?></div>
        <div>Válida até <b><?= esc($p['validade']) ?></b></div>
        <div class="local"><?= esc($emitidoEm) ?><?= $cidadeUf !== '' ? ' • ' . esc($cidadeUf) : '' ?></div>
    </div>
</div>