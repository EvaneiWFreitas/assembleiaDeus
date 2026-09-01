$base = 'http://localhost:8080'
$r = Invoke-WebRequest "$base/login" -SessionVariable s
if ($r.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t = $Matches[1] }
Invoke-WebRequest "$base/login" -Method Post -WebSession $s -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } | Out-Null
$e = Invoke-WebRequest "$base/congregacoes/editar/1" -WebSession $s
if ($e.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t2 = $Matches[1] }
try {
  Invoke-WebRequest "$base/congregacoes/atualizar/1" -Method Post -WebSession $s -Body @{ csrf_test_name = $t2; nome = 'Congregacao Debug X'; codigo = 'CONG-1'; ativo = '1' } -MaximumRedirection 0 -SkipHttpErrorCheck -ErrorAction Stop | Out-Null
} catch {
  $resp = $_.Exception.Response
  Write-Output "redirect: $($resp.Headers.Location)"
}
# verificar persistencia
$l = Invoke-WebRequest "$base/congregacoes?q=Debug" -WebSession $s
if ($l.Content -match 'Congregacao Debug X') { Write-Output 'PERSISTIDA' } else { Write-Output 'NAO persistida' }
