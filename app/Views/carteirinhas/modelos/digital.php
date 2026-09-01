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
    .ml-digital { width: 100%; height: 100%; background: radial-gradient(120% 90% at 85% -10%, #43318f 0%, #1a1430 55%, #0d0a1c 100%); color: #e9e6ff; font-family: 'Segoe UI', Arial, sans-serif; position: relative; overflow: hidden; display: flex; }
    .ml-digital::after { content: ''; position: absolute; right: -30px; top: -30px; width: 120px; height: 120px; border-radius: 50%; background: radial-gradient(circle, rgba(124,199,255,.28), transparent 70%); pointer-events: none; }

    .ml-digital .esq { width: 130px; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; padding: 22px 10px; gap: 14px; }
    .ml-digital .logo { width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,.12); backdrop-filter: blur(2px); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .ml-digital .logo img { width: 90%; height: 90%; object-fit: contain; }
    .ml-digital .logo .placeholder { font-size: 18px; font-weight: 800; color: #c9b3ff; }
    .ml-digital .foto { width: 102px; height: 102px; border-radius: 16px; padding: 3px; background: linear-gradient(140deg, #7c6bff, #3bb6ff); display: flex; align-items: center; justify-content: center; }
    .ml-digital .foto .interior { width: 100%; height: 100%; border-radius: 12px; overflow: hidden; background: #14102a; display: flex; align-items: center; justify-content: center; }
    .ml-digital .foto img { width: 100%; height: 100%; object-fit: cover; }
    .ml-digital .foto .ini { font-size: 40px; font-weight: 800; color: #c9b3ff; }
    .ml-digital .nome-igreja { font-size: 7.5px; text-transform: uppercase; letter-spacing: 1.2px; color: #b9a8f5; text-align: center; }

    .ml-digital .dir { flex: 1; min-width: 0; display: flex; flex-direction: column; padding: 20px 18px 14px 4px; }
    .ml-digital .chip { align-self: flex-start; background: linear-gradient(90deg, #7c6bff, #3bb6ff); color: #fff; font-size: 7.5px; letter-spacing: 2px; text-transform: uppercase; padding: 3px 10px; border-radius: 10px; font-weight: 700; }
    .ml-digital .nome { font-size: 15px; font-weight: 800; line-height: 1.15; margin-top: 8px; color: #fff; }
    .ml-digital .sub { font-size: 8.5px; color: #b9a8f5; margin-top: 3px; }

    .ml-digital .dados { margin-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px 12px; }
    .ml-digital .dados .campo b { display: block; font-size: 7px; text-transform: uppercase; letter-spacing: 1.2px; color: #7c9dff; }
    .ml-digital .dados .campo span { font-size: 9px; color: #e6ecff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }

    .ml-digital .rodape { margin-top: auto; border-top: 1px solid rgba(139,124,255,.35); padding-top: 7px; display: flex; justify-content: space-between; gap: 8px; font-size: 7.5px; color: #9da3c9; }
    .ml-digital .rodape b { color: #c9b3ff; }
    .ml-digital .rodape .local { text-align: right; }
</style>

<div class="ml-digital">
    <div class="esq">
        <div class="logo">
            <?php if ($logoUrl !== ''): ?>
                <img src="<?= esc($logoUrl) ?>" alt="">
            <?php else: ?>
                <div class="placeholder"><?= esc($p['foto_inicial']) ?></div>
            <?php endif; ?>
        </div>
        <div class="foto">
            <div class="interior">
                <?php if ($fotoUrl !== ''): ?>
                    <img src="<?= esc($fotoUrl) ?>" alt="">
                <?php else: ?>
                    <div class="ini"><?= esc($p['foto_inicial']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="nome-igreja"><?= esc($igreja['nome'] ?? '') ?></div>
    </div>

    <div class="dir">
        <div class="chip"><?= esc($p['rotulo']) ?></div>
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
            <div class="local"><?= esc($emitidoEm) ?><?= $cidadeUf !== '' ? ' • ' . esc($cidadeUf) : '' ?></div>
        </div>
    </div>
</div>