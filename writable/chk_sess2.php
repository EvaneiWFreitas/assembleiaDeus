<?php
$pdo = new PDO('mysql:host=localhost;dbname=gestao_igreja', 'root', '');
$row = $pdo->query("SELECT data FROM ci_sessions WHERE id = 'gestao_igreja_session:96c430fd9eb30c4ec09350348516780e'")->fetch();
echo $row ? $row['data'] : 'nao encontrado';
echo PHP_EOL;
