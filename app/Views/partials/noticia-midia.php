<?php
$imagemNoticia = trim((string) ($noticia['imagem'] ?? ''));
$videoNoticia = midiaVideoNoticia((string) ($noticia['video'] ?? ''));
$classeMidia = $classeMidia ?? 'mb-3';
$midiaPadrao = $midiaPadrao ?? false;
?>
<?php if ($imagemNoticia !== '' || $videoNoticia !== null): ?>
    <div class="ad-noticia-midia <?= e($classeMidia) ?>">
        <?php if ($imagemNoticia !== ''): ?>
            <img src="<?= e(url('uploads/' . $imagemNoticia)) ?>" class="ad-noticia-imagem"
                 alt="<?= e($noticia['titulo'] ?? '') ?>" loading="lazy">
        <?php endif; ?>
        <?php if ($videoNoticia !== null): ?>
            <div class="ad-noticia-video ratio ratio-16x9">
                <?php if ($videoNoticia['tipo'] === 'iframe'): ?>
                    <iframe src="<?= e($videoNoticia['src']) ?>"
                            title="Vídeo: <?= e($noticia['titulo'] ?? '') ?>"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen loading="lazy"></iframe>
                <?php else: ?>
                    <video src="<?= e($videoNoticia['src']) ?>" controls preload="metadata"></video>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php elseif ($midiaPadrao): ?>
    <div class="ad-noticia-sem-midia <?= e($classeMidia) ?>">
        <i class="fas fa-newspaper" aria-hidden="true"></i>
    </div>
<?php endif; ?>
