<?php
$pdo = new PDO('mysql:host=localhost;dbname=gestao_igreja', 'root', '');
foreach ($pdo->query('SELECT id, ip_address, timestamp, LEFT(data, 60) AS d FROM ci_sessions ORDER BY timestamp DESC LIMIT 5') as $row) {
    print_r($row);
}
