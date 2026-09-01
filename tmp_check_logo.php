<?php
$db = new mysqli('localhost','root','','gestao_igreja');
$r = $db->query('SELECT id,email,ativo,bloqueado,deve_alterar_senha FROM usuarios');
while($row = $r->fetch_assoc()){var_dump($row);}
