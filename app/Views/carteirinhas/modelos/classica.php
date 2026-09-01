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
    .ml-classica { width: 100%; height: 100%; background: #ffffff; font-family: 'Segoe UI', Arial, sans-serif; position: relative; color: #1a2639; }
    .ml-classica .topo { height: 60px; background: linear-gradient(135deg, #1565c0, #0d47a1); color: #fff; display: flex; align-items: center; gap: 10px; padding: 0 18px; }
    .ml-classica .topo .logo { width: 38px; height: 38px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
    .ml-classica .topo .logo img { width: 100%; height: 100%; object-fit: contain; }
    .ml-classica .topo .logo .placeholder { color: #1565c0; font-size: 18px; font-weight: 700; }
    .ml-classica .topo .dados { line-height: 1.1; }
    .ml-classica .topo .dados .igreja { font-size: 13px; font-weight: 700; letter-spacing: .4px; }
    .ml-classica .topo .dados .sub { font-size: 7.5px; letter-spacing: 2.5px; text-transform: uppercase; opacity: .85; }
    .ml-classica .topo .selo { margin-left: auto; font-size: 8px; letter-spacing: 1px; text-align: right; line-height: 1.4; opacity: .9; }

    .ml-classica .corpo { position: absolute; top: 60px; left: 0; right: 0; bottom: 38px; display: flex; align-items: center; gap: 16px; padding: 0 18px; }
    .ml-classica .foto { width: 130px; height: 130px; border-radius: 50%; border: 3px solid #1565c0; overflow: hidden; background: #eef4ff; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
    .ml-classica .foto img { width: 100%; height: 100%; object-fit: cover; }
    .ml-classica .foto .ini { font-size: 44px; color: #1565c0; font-weight: 700; }
    .ml-classica .info { flex: 1; min-width: 0; }
    .ml-classica .info .nome { font-size: 14px; font-weight: 700; line-height: 1.15; margin-bottom: 4px; }
    .ml-classica .info .cargo { display: inline-block; background: #1565c0; color: #fff; font-size: 8.5px; letter-spacing: 1.5px; text-transform: uppercase; padding: 3px 10px; border-radius: 12px; margin-bottom: 8px; }
    .ml-classica .info .linha { display: flex; justify-content: space-between; gap: 10px; font-size: 9px; line-height: 1.55; border-bottom: 1px dotted #c9d6e8; }
    .ml-classica .info .linha b { color: #1565c0; font-size: 8px; text-transform: uppercase; letter-spacing: .5px; }
    .ml-classica .info .linha span { text-align: right; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .ml-classica .rodape { position: absolute; left: 0; right: 0; bottom: 0; height: 38px; background: #eef2f9; border-top: 2px solid #1565c0; display: flex; align-items: center; justify-content: space-between; padding: 0 18px; font-size: 8.5px; color: #34507a; }
    .ml-classica .rodape b { color: #0d47a1; }
    .ml-classica .rodape .local { text-align: right; }
</style>

<div class="ml-classica">
    <div class="topo">
        <div class="logo">
            <?php if ($logoUrl !== ''): ?>
                <img src="<?= esc($logoUrl) ?>" alt="">
            <?php else: ?>
                <div class="placeholder"><?= esc($p['foto_inicial']) ?></div>
            <?php endif; ?>
        </div>
        <div class="dados">
            <div class="igreja"><?= esc($igreja['nome'] ?? '') ?></div>
            <div class="sub">Carteirinha de Identificação</div>
        </div>
        <div class="selo">MEMBRO<br><?= esc($p['id']) ?></div>
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
        <div class="local">
            Emitida em <?= esc($emitidoEm) ?><?= $cidadeUf !== '' ? ' • ' . esc($cidadeUf) : '' ?>
        </div>
    </div>
</div>