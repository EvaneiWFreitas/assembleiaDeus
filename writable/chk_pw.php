<?php
$m = new mysqli('localhost', 'root', '', 'gestao_igreja');
$r = $m->query("SELECT email, senha_hash FROM usuarios WHERE email = 'teste@dev.local'");
$row = $r->fetch_assoc();
var_dump(password_verify('teste12345', $row['senha_hash']));
var_dump($row['senha_hash']);
