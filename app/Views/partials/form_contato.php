<?php
$nome     = old('nome', '');
$email    = old('email', '');
$telefone = old('telefone', '');
$assunto  = old('assunto', '');
$mensagem = old('mensagem', '');
?>
<form action="<?= e(url('contato/enviar')) ?>" method="post" class="ad-form-contato" novalidate>
    <?= App\Core\Csrf::field() ?>

    <div class="row g-3">
        <div class="col-md-6">
            <label for="contato_nome" class="form-label">Nome completo <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="contato_nome" name="nome" required
                   maxlength="150" value="<?= e($nome) ?>" placeholder="Seu nome completo">
        </div>
        <div class="col-md-6">
            <label for="contato_email" class="form-label">E-mail <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="contato_email" name="email" required
                   maxlength="190" value="<?= e($email) ?>" placeholder="voce@exemplo.com">
        </div>
        <div class="col-md-6">
            <label for="contato_telefone" class="form-label">Telefone / WhatsApp</label>
            <input type="tel" class="form-control" id="contato_telefone" name="telefone"
                   maxlength="20" value="<?= e($telefone) ?>" placeholder="(00) 00000-0000">
        </div>
        <div class="col-md-6">
            <label for="contato_assunto" class="form-label">Assunto</label>
            <input type="text" class="form-control" id="contato_assunto" name="assunto"
                   maxlength="180" value="<?= e($assunto) ?>" placeholder="Como podemos ajudar?">
        </div>
        <div class="col-12">
            <label for="contato_mensagem" class="form-label">Mensagem <span class="text-danger">*</span></label>
            <textarea class="form-control" id="contato_mensagem" name="mensagem" rows="5" required
                      maxlength="5000" placeholder="Escreva sua mensagem..."><?= e($mensagem) ?></textarea>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="contato_lgpd" name="lgpd" value="1" required>
                <label class="form-check-label small" for="contato_lgpd">
                    Concordo com o tratamento dos meus dados pessoais para fins de contato, conforme a LGPD.
                </label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-ad-primary">
                <i class="fas fa-paper-plane me-2"></i>Enviar mensagem
            </button>
        </div>
    </div>
</form>
