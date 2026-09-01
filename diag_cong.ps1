$base = 'http://localhost:8080'
$r = Invoke-WebRequest "$base/login" -SessionVariable sess -TimeoutSec 10 -UseBasicParsing
$s = $sess
$t = ([regex]::Match($r.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"')).Groups[1].Value
Invoke-WebRequest "$base/login" -Method Post -WebSession $s -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } -UseBasicParsing | Out-Null

$cod = 'TST-DIAG-' + (Get-Date -Format 'HHmmss')
$n = Invoke-WebRequest "$base/congregacoes/novo" -WebSession $s -UseBasicParsing
$m = [regex]::Match($n.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"')
Invoke-WebRequest "$base/congregacoes/salvar" -Method Post -WebSession $s -Body @{
    csrf_test_name = $m.Groups[1].Value; nome = 'Diag Cong'; codigo = $cod
    telefone = ''; email = ''; cep = ''; logradouro = ''; numero = ''
    bairro = ''; cidade = 'Teste'; estado = 'SP'; data_abertura = ''; ativo = '1'
} -UseBasicParsing | Out-Null

$c2 = Invoke-WebRequest "$base/congregacoes?q=$cod" -WebSession $s -UseBasicParsing
$id = [regex]::Match($c2.Content, 'congregacoes/editar/(\d+)').Groups[1].Value
Write-Output "ID criado: $id"

$e = Invoke-WebRequest "$base/congregacoes/editar/$id" -WebSession $s -UseBasicParsing
$t3 = [regex]::Match($e.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"').Groups[1].Value

try {
    $up = Invoke-WebRequest "$base/congregacoes/atualizar/$id" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t3; nome = 'Diag Cong EDITADA'; codigo = $cod
        telefone = ''; email = ''; cep = ''; logradouro = ''; numero = ''
        bairro = ''; cidade = 'Teste'; estado = 'SP'; data_abertura = ''; ativo = '1'
    } -UseBasicParsing
    Write-Output "UP status: $($up.StatusCode) | tem 'sucesso': $($up.Content -match 'sucesso') | tem 'erros': $($up.Content -match 'erros')"
} catch {
    Write-Output "UP ERRO HTTP: $($_.Exception.Response.StatusCode.value__)"
}

$c3 = Invoke-WebRequest "$base/congregacoes?q=$cod" -WebSession $s -UseBasicParsing
Write-Output "Editada visivel na listagem: $($c3.Content -match 'EDITADA')"

# limpeza
Invoke-WebRequest "$base/congregacoes/excluir/$id" -Method Post -WebSession $s -Body @{ csrf_test_name = $t3 } -UseBasicParsing | Out-Null
Write-Output "Limpeza: registro de teste excluido"
