<?php
// Teste de módulos: login + GET nas páginas + POST de cadastro
$base = 'http://localhost/assembleiaDeus/public';
$ck = __DIR__ . '/tmp_cookie.txt';

function get($url, $ck) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_COOKIEJAR=>$ck, CURLOPT_COOKIEFILE=>$ck, CURLOPT_FOLLOWLOCATION=>false, CURLOPT_TIMEOUT=>20]);
    $r = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    return [$code, $r];
}
function post($url, $data, $ck) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_COOKIEJAR=>$ck, CURLOPT_COOKIEFILE=>$ck, CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>http_build_query($data), CURLOPT_FOLLOWLOCATION=>false, CURLOPT_TIMEOUT=>20]);
    $r = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $loc = curl_getinfo($ch, CURLINFO_REDIRECT_URL); curl_close($ch);
    return [$code, $r, $loc];
}

// 1. pega CSRF do login
[$code, $loginHtml] = get("$base/login", $ck);
preg_match('/name="csrf_test_name"\s+value="([^"]+)"/', $loginHtml, $m);
$csrf = $m[1] ?? '';
echo "login page: $code, csrf: " . ($csrf ? 'ok' : 'NAO') . "\n";

// 2. login
[$code, $body, $loc] = post("$base/login", ['csrf_test_name'=>$csrf, 'email'=>'teste.dev@local', 'senha'=>'teste12345'], $ck);
echo "login POST: $code -> $loc\n";

// 3. páginas dos módulos
foreach (['dashboard','departamentos','ministerios','celulas','discipulos'] as $mod) {
    [$code, $html] = get("$base/$mod", $ck);
    $title = '';
    preg_match('/<title>([^<]*)<\/title>/', $html, $t); $title = $t[1] ?? '';
    $has404 = strpos($html, '404') !== false || $code == 404;
    echo str_pad($mod, 15) . "GET: $code  404?" . ($has404?'SIM':'nao') . "  title: $title\n";
}
