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
    .ml-moderna { width: 100%; height: 100%; background: #ffffff; font-family: 'Segoe UI', Arial, sans-serif; position: relative; overflow: hidden; display: flex; color: #14251b; }

    .ml-moderna .faixa { width: 118px; background: linear-gradient(160deg, #1b8a4c, #0f5c33); color: #fff; display: flex; flex-direction: column; align-items: center; padding: 14px 8px 10px; flex-shrink: 0; }
    .ml-moderna .faixa .logo { width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,.16); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .ml-moderna .faixa .logo img { width: 90%; height: 90%; object-fit: contain; }
    .ml-moderna .faixa .logo .placeholder { font-size: 20px; font-weight: 700; }
    .ml-moderna .faixa .foto { margin-top: 16px; width: 92px; height: 92px; border-radius: 14px; background: rgba(255,255,255,.14); border: 2px solid rgba(255,255,255,.6); overflow: hidden; display: flex; align-items: center; justify-content: center; }
    .ml-moderna .faixa .foto img { width: 100%; height: 100%; object-fit: cover; }
    .ml-moderna .faixa .foto .ini { font-size: 34px; font-weight: 700; color: #fff; }
    .ml-moderna .faixa .nome-igreja { margin-top: auto; font-size: 8px; text-align: center; line-height: 1.3; text-transform: uppercase; letter-spacing: 1px; opacity: .95; }

    .ml-moderna .area { flex: 1; min-width: 0; display: flex; flex-direction: column; padding: 16px 16px 12px; }
    .ml-moderna .cargo { align-self: flex-start; background: #1b8a4c; color: #fff; font-size: 8px; letter-spacing: 1.5px; text-transform: uppercase; padding: 3px 10px; border-radius: 12px; }
    .ml-moderna .nome { font-size: 15px; font-weight: 800; line-height: 1.15; margin-top: 6px; color: #0f3d24; }
    .ml-moderna .sub { font-size: 8.5px; color: #6b7a6f; margin-top: 2px; }

    .ml-moderna .dados { margin-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 7px 12px; }
    .ml-moderna .dados .campo b { display: block; font-size: 7px; text-transform: uppercase; letter-spacing: 1px; color: #1b8a4c; }
    .ml-moderna .dados .campo span { font-size: 9px; color: #24332a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }

    .ml-moderna .rodape { margin-top: auto; border-top: 1px solid #e0e8e2; padding-top: 7px; display: flex; justify-content: space-between; gap: 8px; font-size: 7.5px; color: #5a6b60; }
    .ml-moderna .rodape b { color: #0f5c33; }
    .ml-moderna .rodape .local { text-align: right; }
</style>

<div class="ml-moderna">
    <div class="faixa">
        <div class="logo">
            <?php if ($logoUrl !== ''): ?>
                <img src="<?= esc($logoUrl) ?>" alt="">
            <?php else: ?>
                <div class="placeholder"><?= esc($p['foto_inicial']) ?></div>
            <?php endif; ?>
        </div>
        <div class="foto">
            <?php if ($fotoUrl !== ''): ?>
                <img src="<?= esc($fotoUrl) ?>" alt="">
            <?php else: ?>
                <div class="ini"><?= esc($p['foto_inicial']) ?></div>
            <?php endif; ?>
        </div>
        <div class="nome-igreja"><?= esc($igreja['nome'] ?? '') ?></div>
    </div>

    <div class="area">
        <div class="cargo"><?= esc($p['rotulo']) ?></div>
        <div class="nome"><?= esc($p['nome']) ?></div>
        <div class="sub"><?= esc($p['tipo_membro']) ?></div>

        <div class="dados">
            <div class="campo"><b>CPF</b><span><?= esc($p['cpf']) ?></span></div>
            <div class="campo"><b>RG</b><span><?= $p['rg'] !== '' ? esc($p['rg']) : '—' ?></span></div>
            <div class="campo"><b>Nascimento</b><span><?= esc($p['nascimento']) ?></span></div>
            <?php if ($p['congregacao'] !== ''): ?>
                <div class="campo"><b>Congregação</b><span><?= esc($p['congregacao']) ?></span></div>
            <?php endif; ?>
            <?php if ($p['numero_registro'] !== ''): ?>
                <div class="campo"><b>Registro</b><span>Nº <?= esc($p['numero_registro']) ?></span></div>
            <?php endif; ?>
            <?php if ($p['telefone'] !== ''): ?>
                <div class="campo"><b>Telefone</b><span><?= esc($p['telefone']) ?></span></div>
            <?php endif; ?>
        </div>

        <div class="rodape">
            <div>Válida até <b><?= esc($p['validade']) ?></b></div>
            <div class="local">Emitida em <?= esc($emitidoEm) ?><?= $cidadeUf !== '' ? '<br>' . esc($cidadeUf) : '' ?></div>
        </div>
    </div>
</div>