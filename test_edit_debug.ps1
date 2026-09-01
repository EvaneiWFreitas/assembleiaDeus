$base = 'http://localhost:8080'
$ProgressPreference = 'SilentlyContinue'
$r = Invoke-WebRequest "$base/login" -SessionVariable sess -UseBasicParsing
$t = [regex]::Match($r.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"').Groups[1].Value
Invoke-WebRequest "$base/login" -Method Post -WebSession $sess -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } -TimeoutSec 10 | Out-Null
Write-Output 'login ok'

$cod = 'TST-' + (Get-Date -Format 'HHmmss')
$n = Invoke-WebRequest "$base/congregacoes/novo" -WebSession $sess -UseBasicParsing
$m = [regex]::Match($n.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"').Groups[1].Value
Invoke-WebRequest "$base/congregacoes/salvar" -Method Post -WebSession $sess -Body @{ csrf_test_name = $m; nome = 'Cong X'; codigo = $cod; ativo = '1' } -MaximumRedirection 5 | Out-Null

$c2 = Invoke-WebRequest "$base/congregacoes?q=$cod" -WebSession $sess -UseBasicParsing
$id = [regex]::Match($c2.Content, 'congregacoes/editar/(\d+)').Groups[1].Value
Write-Output "id=$id"

$e = Invoke-WebRequest "$base/congregacoes/editar/$id" -WebSession $sess -UseBasicParsing
$t3 = [regex]::Match($e.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"').Groups[1].Value
Invoke-WebRequest "$base/congregacoes/atualizar/$id" -Method Post -WebSession $sess -Body @{ csrf_test_name = $t3; nome = 'Cong Y EDITADA'; codigo = $cod; ativo = '1' } -MaximumRedirection 5 | Out-Null

$c3 = Invoke-WebRequest "$base/congregacoes?q=$cod" -WebSession $sess -UseBasicParsing
if ($c3.Content -match 'Cong Y EDITADA') { Write-Output 'EDITADA aparece' } else { Write-Output 'nao aparece' }
