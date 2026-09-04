<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<?php if (empty($diretorias)): ?>
    <div class="col-12 text-center text-muted py-5">
        <i class="fa-solid fa-user-tie fa-2x mb-3 text-azul"></i>
        <p class="mb-0">Em breve apresentaremos os membros da nossa diretoria. Fique atento!</p>
    </div>
<?php else: ?>
    <div class="row justify-content-center g-4">
        <?php foreach ($diretorias as $d): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-valor p-4 text-center h-100">
                    <?php if (! empty($d['foto']) && is_file(ROOTPATH . 'public/uploads/diretorias/' . $d['foto'])): ?>
                        <img src="<?= base_url('uploads/diretorias/' . $d['foto']) ?>" alt="<?= esc($d['nome']) ?>"
                             class="rounded-circle mx-auto mb-3" style="width:110px;height:110px;object-fit:cover;border:3px solid var(--azul-claro);">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width:110px;height:110px;font-size:2.4rem;border:3px solid var(--azul-claro);">
                            <?= mb_strimwidth(esc($d['nome']), 0, 2, '', 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <h5 class="fw-semibold mb-1"><?= esc($d['nome']) ?></h5>
                    <span class="badge badge-azul mb-3"><?= esc($d['cargo']) ?></span>

                    <div class="d-flex justify-content-center gap-3 mt-auto">
                        <?php if (! empty($d['whatsapp'])): ?>
                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $d['whatsapp']) ?>" target="_blank" rel="noopener"
                               class="text-decoration-none" title="WhatsApp">
                                <i class="fa-brands fa-whatsapp fa-lg" style="color:#25d366;"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (! empty($d['email'])): ?>
                            <a href="mailto:<?= esc($d['email']) ?>" class="text-decoration-none" title="E-mail">
                                <i class="fa-solid fa-envelope fa-lg" style="color:var(--azul);"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (! empty($d['telefone'])): ?>
                            <a href="tel:<?= preg_replace('/\D/', '', $d['telefone']) ?>" class="text-decoration-none" title="Telefone">
                                <i class="fa-solid fa-phone fa-lg" style="color:var(--cinza);"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
