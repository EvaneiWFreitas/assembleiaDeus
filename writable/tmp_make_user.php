<?php
// script temporário: cria/redefine usuário de teste
$mysqli = new mysqli('localhost', 'root', '', 'gestao_igreja');
if ($mysqli->connect_error) { die('conn fail: '.$mysqli->connect_error); }
$hash = password_hash('teste12345', PASSWORD_DEFAULT);
$email = 'teste@dev.local';
$res = $mysqli->query("SELECT id FROM usuarios WHERE email='teste.dev@local'");
if ($res && $res->num_rows) {
    $mysqli->query("UPDATE usuarios SET email='teste@dev.local', senha_hash='$hash', ativo=1, bloqueado=0, bloqueado_ate=NULL, tentativas_login=0, deve_alterar_senha=0 WHERE email='teste.dev@local'");
    echo "updated\n";
} else {
    $roleRes = $mysqli->query("SELECT id FROM roles ORDER BY id ASC LIMIT 1");
    $roleId = $roleRes ? (int)$roleRes->fetch_assoc()['id'] : 1;
    $mysqli->query("INSERT INTO usuarios (nome, email, senha_hash, role_id, ativo, deve_alterar_senha) VALUES ('Teste Dev','$email','$hash',$roleId,1,0)");
    echo "inserted (role $roleId)\n";
}
echo "ok\n";
