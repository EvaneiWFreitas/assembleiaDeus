$base = 'http://localhost:8080'

# Login
$r = Invoke-WebRequest "$base/login" -SessionVariable sess -TimeoutSec 10 -UseBasicParsing
$s = $sess
$t = if ($r.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $Matches[1] } else { '' }
Invoke-WebRequest "$base/login" -Method Post -WebSession $s -TimeoutSec 10 -UseBasicParsing -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } | Out-Null
Write-Output "== CONGREGACOES =="
$cod = "TST-AUTO-" + (Get-Date -Format 'HHmmss')

# 1. Listagem
$c = Invoke-WebRequest "$base/congregacoes" -WebSession $s -TimeoutSec 10 -UseBasicParsing
if ($c.Content -match 'Congrega') { Write-Output "1. Listagem OK" } else { Write-Output "1. Listagem FALHOU (status $($c.StatusCode))" }

# 2. Form novo
$n = Invoke-WebRequest "$base/congregacoes/novo" -WebSession $s -TimeoutSec 10 -UseBasicParsing
if ($n.Content -match 'Nome') { Write-Output "2. Form novo OK" } else { Write-Output "2. Form novo FALHOU (status $($n.StatusCode))" }

# 3. Salvar nova
$m = [regex]::Match($n.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"')
$sv = Invoke-WebRequest "$base/congregacoes/salvar" -Method Post -WebSession $s -TimeoutSec 10 -UseBasicParsing -Body @{
    csrf_test_name = $m.Groups[1].Value
    nome = 'Congregacao Teste Auto'
    codigo = $cod
    telefone = ''; email = ''; cep = ''; logradouro = ''; numero = ''
    bairro = ''; cidade = 'Teste'; estado = 'SP'; data_abertura = ''
    ativo = '1'
} -MaximumRedirection 5
if ($sv.Content -match 'sucesso') { Write-Output "3. Salvar OK" } else { Write-Output "3. Salvar: status $($sv.StatusCode)" }

# 4. Descobrir id da criada via busca
$c2 = Invoke-WebRequest "$base/congregacoes?q=TST-AUTO" -WebSession $s -TimeoutSec 10 -UseBasicParsing
$editId = $null
if ($c2.Content -match 'congregacoes/editar/(\d+)') { $editId = $Matches[1] }
if ($editId) { Write-Output "4. Congregacao criada encontrada (id $editId)" }
else { Write-Output "4. FALHOU: criada nao encontrada na listagem"; $editId = 1 }

# 5. Form editar
$e = Invoke-WebRequest "$base/congregacoes/editar/$editId" -WebSession $s -TimeoutSec 10 -UseBasicParsing
if ($e.Content -match 'Editar') { Write-Output "5. Form editar OK" } else { Write-Output "5. Form editar FALHOU (status $($e.StatusCode))" }

# 6. Atualizar (altera nome)
$t3 = [regex]::Match($e.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"').Groups[1].Value
$nomeNovo = 'Congregacao Teste Auto EDITADA'
$up = Invoke-WebRequest "$base/congregacoes/atualizar/$editId" -Method Post -WebSession $s -TimeoutSec 10 -UseBasicParsing -Body @{
    csrf_test_name = $t3
    nome = $nomeNovo
    codigo = $cod
    telefone = ''; email = ''; cep = ''; logradouro = ''; numero = ''
    bairro = ''; cidade = 'Teste'; estado = 'SP'; data_abertura = ''
    ativo = '1'
} -MaximumRedirection 5
if ($up.Content -match 'sucesso') { Write-Output "6. Atualizar OK" } else { Write-Output "6. Atualizar: status $($up.StatusCode)" }

# 7. Confirma edicao persistida
$c3 = Invoke-WebRequest "$base/congregacoes?q=TST-AUTO" -WebSession $s -TimeoutSec 10 -UseBasicParsing
if ($c3.Content -match [regex]::Escape($nomeNovo)) { Write-Output "7. Edicao persistida OK" } else { Write-Output "7. FALHOU: nome editado nao aparece na listagem" }

# 8. Excluir
$ex = Invoke-WebRequest "$base/congregacoes/excluir/$editId" -Method Post -WebSession $s -TimeoutSec 10 -UseBasicParsing -Body @{ csrf_test_name = $t3 } -MaximumRedirection 5
if ($ex.Content -match 'sucesso') { Write-Output "8. Excluir OK" } else { Write-Output "8. Excluir: status $($ex.StatusCode)" }

