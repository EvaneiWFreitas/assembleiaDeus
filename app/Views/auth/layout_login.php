<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Gestão de Igrejas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
    <style>
        body { background: linear-gradient(135deg,#0d6efd 0%,#0a3d91 100%); min-height:100vh; display:flex; align-items:center; }
        .card-login { max-width: 420px; width: 100%; border: none; border-radius: 1rem; }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="card card-login shadow p-4">
        <?= $this->renderSection('conteudo') ?>
    </div>
</div>
<script>
    document.getElementById('btnVerSenha')?.addEventListener('click', function () {
        var input = document.querySelector('input[name="senha"]');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
